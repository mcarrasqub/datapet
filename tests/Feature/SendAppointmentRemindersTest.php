<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentReminder;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SendAppointmentRemindersTest extends TestCase
{
    use RefreshDatabase;

    // ── Líneas 18-19, 21-28, 30-31, 63-66 ──────────────────────────────────
    // Cita mañana → envía recordatorio con driver log

    public function test_sends_reminder_for_appointment_tomorrow_using_log_driver(): void
    {
        Config::set('services.whatsapp.driver', 'log');

        Log::shouldReceive('info')->once()->withArgs(function ($message) {
            return str_contains($message, '[WHATSAPP RECORDATORIO]')
                && str_contains($message, 'Ziggy');
        });

        $client = User::factory()->create([
            'role'  => 'client',
            'phone' => '573000000000',
            'name'  => 'Mariana',
        ]);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $pet    = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Ziggy']);

        Appointment::create([
            'doctor_id'  => $doctor->id,
            'pet_id'     => $pet->id,
            'date'       => Carbon::today()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time'   => '09:30:00',
            'status'     => 'scheduled',
            'reason'     => 'Chequeo',
        ]);

        $this->artisan('reminders:appointments')->assertExitCode(0);

        $this->assertDatabaseHas('appointment_reminders', [
            'phone'  => '573000000000',
            'status' => 'sent',
        ]);
    }

    // ── Líneas 18-19, 21-28 ─────────────────────────────────────────────────
    // Cita pasado mañana → también envía

    public function test_sends_reminder_for_appointment_in_two_days_using_log_driver(): void
    {
        Config::set('services.whatsapp.driver', 'log');

        Log::shouldReceive('info')->once()->withArgs(function ($message) {
            return str_contains($message, '[WHATSAPP RECORDATORIO]')
                && str_contains($message, 'Rex');
        });

        $client = User::factory()->create([
            'role'  => 'client',
            'phone' => '573111111111',
        ]);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $pet    = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Rex']);

        Appointment::create([
            'doctor_id'  => $doctor->id,
            'pet_id'     => $pet->id,
            'date'       => Carbon::today()->addDays(2)->toDateString(),
            'start_time' => '10:00:00',
            'end_time'   => '10:30:00',
            'status'     => 'scheduled',
        ]);

        $this->artisan('reminders:appointments')->assertExitCode(0);

        $this->assertDatabaseHas('appointment_reminders', [
            'phone'  => '573111111111',
            'status' => 'sent',
        ]);
    }

    // ── Líneas 63-66 con UltraMsg ────────────────────────────────────────────
    // Prueba el envío real por HTTP a UltraMsg

    public function test_sends_reminder_using_ultramsg_driver(): void
    {
        Config::set('services.whatsapp.driver', 'ultramsg');
        Config::set('services.whatsapp.ultramsg.instance', 'inst123');
        Config::set('services.whatsapp.ultramsg.token', 'tok456');

        Http::fake([
            'api.ultramsg.com/*' => Http::response(['success' => true], 200),
        ]);

        $client = User::factory()->create([
            'role'  => 'client',
            'phone' => '573000000000',
        ]);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $pet    = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Noodle']);

        Appointment::create([
            'doctor_id'  => $doctor->id,
            'pet_id'     => $pet->id,
            'date'       => Carbon::today()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time'   => '09:30:00',
            'status'     => 'scheduled',
        ]);

        $this->artisan('reminders:appointments')->assertExitCode(0);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'inst123/messages/chat')
                && $request['token'] === 'tok456'
                && $request['to'] === '573000000000'
                && str_contains($request['body'], 'Noodle');
        });

        $this->assertDatabaseHas('appointment_reminders', [
            'phone'  => '573000000000',
            'status' => 'sent',
        ]);
    }

    // ── Línea 34: continue cuando no hay mascota o dueño ────────────────────
    // Cubre el if (!$pet || !$user) → continue

    public function test_skips_appointment_when_pet_has_no_owner(): void
{
    Config::set('services.whatsapp.driver', 'log');

    $doctor = User::factory()->create(['role' => 'doctor']);
    $client = User::factory()->create(['role' => 'client', 'phone' => '573000000000']);
    $pet    = Pet::factory()->create(['user_id' => $client->id]);

    Appointment::create([
        'doctor_id'  => $doctor->id,
        'pet_id'     => $pet->id,
        'date'       => Carbon::today()->addDay()->toDateString(),
        'start_time' => '09:00:00',
        'end_time'   => '09:30:00',
        'status'     => 'scheduled',
        'reason'     => 'Control',
    ]);

    // Borramos el dueño directo en BD para que pet->owner retorne null
    \DB::table('users')->where('id', $client->id)->delete();

    $this->artisan('reminders:appointments')->assertExitCode(0);

    $this->assertDatabaseCount('appointment_reminders', 0);
}

    // ── Línea 53: continue cuando el usuario no tiene teléfono ──────────────
    // Cubre el if (!$phone) → continue

    public function test_skips_appointment_when_user_has_no_phone(): void
    {
        Config::set('services.whatsapp.driver', 'log');

        $client = User::factory()->create([
            'role'  => 'client',
            'phone' => null,
        ]);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $pet    = Pet::factory()->create(['user_id' => $client->id]);

        Appointment::create([
            'doctor_id'  => $doctor->id,
            'pet_id'     => $pet->id,
            'date'       => Carbon::today()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time'   => '09:30:00',
            'status'     => 'scheduled',
        ]);

        $this->artisan('reminders:appointments')->assertExitCode(0);

        $this->assertDatabaseCount('appointment_reminders', 0);
    }

    // ── Líneas 38-39 y 42-45: continue cuando ya existe recordatorio ─────────
    // Cubre el if ($yaExiste) → continue (no duplica)

    public function test_does_not_duplicate_reminder_if_already_sent(): void
    {
        Config::set('services.whatsapp.driver', 'log');

        Log::shouldReceive('info')->once()->withArgs(function ($message) {
            return str_contains($message, '[WHATSAPP RECORDATORIO]');
        });

        $client = User::factory()->create([
            'role'  => 'client',
            'phone' => '573000000000',
        ]);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $pet    = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Luna']);

        $appointment = Appointment::create([
            'doctor_id'  => $doctor->id,
            'pet_id'     => $pet->id,
            'date'       => Carbon::today()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time'   => '09:30:00',
            'status'     => 'scheduled',
        ]);

        // Primera corrida: envía
        $this->artisan('reminders:appointments')->assertExitCode(0);
        $this->assertDatabaseCount('appointment_reminders', 1);

        // Segunda corrida: NO duplica (cubre líneas 38-45)
        $this->artisan('reminders:appointments')->assertExitCode(0);
        $this->assertDatabaseCount('appointment_reminders', 1);
    }

    // ── Citas que NO deben procesarse (where status=scheduled) ──────────────
    // Cubre que el query filtra correctamente canceladas

    public function test_does_not_send_reminder_for_canceled_appointment(): void
    {
        Config::set('services.whatsapp.driver', 'log');

        $client = User::factory()->create([
            'role'  => 'client',
            'phone' => '573000000000',
        ]);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $pet    = Pet::factory()->create(['user_id' => $client->id]);

        Appointment::create([
            'doctor_id'  => $doctor->id,
            'pet_id'     => $pet->id,
            'date'       => Carbon::today()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time'   => '09:30:00',
            'status'     => 'canceled',
        ]);

        $this->artisan('reminders:appointments')->assertExitCode(0);

        $this->assertDatabaseCount('appointment_reminders', 0);
    }

    // ── Cita de hoy no debe procesarse (fuera del rango 1-2 días) ───────────

    public function test_does_not_send_reminder_for_appointment_today(): void
    {
        Config::set('services.whatsapp.driver', 'log');

        $client = User::factory()->create([
            'role'  => 'client',
            'phone' => '573000000000',
        ]);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $pet    = Pet::factory()->create(['user_id' => $client->id]);

        Appointment::create([
            'doctor_id'  => $doctor->id,
            'pet_id'     => $pet->id,
            'date'       => Carbon::today()->toDateString(),
            'start_time' => '09:00:00',
            'end_time'   => '09:30:00',
            'status'     => 'scheduled',
        ]);

        $this->artisan('reminders:appointments')->assertExitCode(0);

        $this->assertDatabaseCount('appointment_reminders', 0);
    }

    // ── Línea 51: mensaje incluye nombre mascota, fecha y hora ───────────────
    // Cubre que el mensaje se construye correctamente

    public function test_reminder_message_contains_pet_name_date_and_time(): void
    {
        Config::set('services.whatsapp.driver', 'log');

        Log::shouldReceive('info')->once()->withArgs(function ($message) {
            return str_contains($message, 'Firulais')
                && str_contains($message, Carbon::today()->addDay()->format('d/m/Y'))
                && str_contains($message, '14:00');
        });

        $client = User::factory()->create([
            'role'  => 'client',
            'phone' => '573000000000',
        ]);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $pet    = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Firulais']);

        Appointment::create([
            'doctor_id'  => $doctor->id,
            'pet_id'     => $pet->id,
            'date'       => Carbon::today()->addDay()->toDateString(),
            'start_time' => '14:00:00',
            'end_time'   => '14:30:00',
            'status'     => 'scheduled',
        ]);

        $this->artisan('reminders:appointments')->assertExitCode(0);
    }
}