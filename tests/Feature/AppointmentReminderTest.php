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

class AppointmentReminderTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function crearCita(string $fecha, string $status = 'scheduled', ?string $phone = '573000000000'): Appointment
    {
        $client = User::factory()->create([
            'role'  => 'client',
            'phone' => $phone,
            'name'  => 'Mariana',
        ]);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $pet    = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Ziggy']);

        return Appointment::create([
            'doctor_id'  => $doctor->id,
            'pet_id'     => $pet->id,
            'date'       => $fecha,
            'start_time' => '09:00:00',
            'end_time'   => '09:30:00',
            'status'     => $status,
            'reason'     => 'Chequeo general',
        ]);
    }

    // ── Comando artisan con driver log ───────────────────────────────────────

    public function test_artisan_command_detects_appointment_tomorrow_using_log_driver(): void
{
    Config::set('services.whatsapp.driver', 'log');

    $this->crearCita(Carbon::today()->addDay()->toDateString());

    $this->artisan('reminders:appointments')->assertExitCode(0);

    $this->assertDatabaseHas('appointment_reminders', [
        'status' => 'sent',
        'phone'  => '573000000000',
    ]);
}

    public function test_artisan_command_detects_appointment_in_two_days_using_log_driver(): void
{
    Config::set('services.whatsapp.driver', 'log');

    $this->crearCita(Carbon::today()->addDays(2)->toDateString());

    $this->artisan('reminders:appointments')->assertExitCode(0);

    $this->assertDatabaseHas('appointment_reminders', [
        'status' => 'sent',
        'phone'  => '573000000000',
    ]);
}

    // ── Comando artisan con driver UltraMsg ──────────────────────────────────

    public function test_artisan_command_sends_real_whatsapp_using_ultramsg_driver(): void
{
    Config::set('services.whatsapp.driver', 'ultramsg');
    Config::set('services.whatsapp.ultramsg.instance', 'inst123');
    Config::set('services.whatsapp.ultramsg.token', 'tok456');

    Http::fake([
        'api.ultramsg.com/*' => Http::response(['success' => true], 200),
    ]);

    $this->crearCita(Carbon::today()->addDay()->toDateString());

    $this->artisan('reminders:appointments')->assertExitCode(0);

    $this->assertDatabaseHas('appointment_reminders', [
        'phone'  => '573000000000',
        'status' => 'sent',
    ]);
}

    // ── Casos donde NO debe enviar ───────────────────────────────────────────

    public function test_artisan_command_does_not_send_reminder_for_appointment_today(): void
{
    Config::set('services.whatsapp.driver', 'log');

    $this->crearCita(Carbon::today()->toDateString());

    $this->artisan('reminders:appointments')->assertExitCode(0);

    $this->assertDatabaseCount('appointment_reminders', 0);
}

    public function test_artisan_command_does_not_send_reminder_for_canceled_appointment(): void
{
    Config::set('services.whatsapp.driver', 'log');

    $this->crearCita(Carbon::today()->addDay()->toDateString(), 'canceled');

    $this->artisan('reminders:appointments')->assertExitCode(0);

    $this->assertDatabaseCount('appointment_reminders', 0);
}

    public function test_artisan_command_does_not_send_if_user_has_no_phone(): void
{
    Config::set('services.whatsapp.driver', 'log');

    $this->crearCita(Carbon::today()->addDay()->toDateString(), 'scheduled', null);

    $this->artisan('reminders:appointments')->assertExitCode(0);

    $this->assertDatabaseCount('appointment_reminders', 0);
}

    // ── No duplicar recordatorios ────────────────────────────────────────────

    public function test_artisan_command_does_not_duplicate_reminders(): void
{
    Config::set('services.whatsapp.driver', 'log');

    $this->crearCita(Carbon::today()->addDay()->toDateString());

    $this->artisan('reminders:appointments')->assertExitCode(0);
    $this->assertDatabaseCount('appointment_reminders', 1);

    $this->artisan('reminders:appointments')->assertExitCode(0);
    $this->assertDatabaseCount('appointment_reminders', 1);
}

    // ── Envío al crear cita (AppointmentController@store) ───────────────────

    public function test_creating_appointment_sends_whatsapp_immediately(): void
    {
        Config::set('services.whatsapp.driver', 'ultramsg');
        Config::set('services.whatsapp.ultramsg.instance', 'inst123');
        Config::set('services.whatsapp.ultramsg.token', 'tok456');

        Http::fake([
            'api.ultramsg.com/*' => Http::response(['success' => true], 200),
        ]);

        $admin  = User::factory()->create(['role' => 'admin']);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $client = User::factory()->create(['role' => 'client', 'phone' => '573000000000']);
        $pet    = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Rex']);

        $response = $this->actingAs($admin)
            ->post(route('appointments.store'), [
                'doctor_id'  => $doctor->id,
                'pet_id'     => $pet->id,
                'date'       => Carbon::today()->addDays(5)->format('Y-m-d'),
                'start_time' => '10:00',
                'end_time'   => '10:30',
                'status'     => 'scheduled',
                'reason'     => 'Vacuna',
            ]);

        $response->assertRedirect(route('appointments.index'));

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'inst123/messages/chat')
                && $request['to'] === '573000000000'
                && str_contains($request['body'], 'Rex');
        });

        $this->assertDatabaseHas('appointment_reminders', [
            'pet_id' => $pet->id,
            'status' => 'sent',
        ]);
    }

    public function test_creating_appointment_without_phone_does_not_send_whatsapp(): void
    {
        Config::set('services.whatsapp.driver', 'ultramsg');
        Config::set('services.whatsapp.ultramsg.instance', 'inst123');
        Config::set('services.whatsapp.ultramsg.token', 'tok456');

        Http::fake([
            'api.ultramsg.com/*' => Http::response(['success' => true], 200),
        ]);

        $admin  = User::factory()->create(['role' => 'admin']);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $client = User::factory()->create(['role' => 'client', 'phone' => null]);
        $pet    = Pet::factory()->create(['user_id' => $client->id]);

        $this->actingAs($admin)
            ->post(route('appointments.store'), [
                'doctor_id'  => $doctor->id,
                'pet_id'     => $pet->id,
                'date'       => Carbon::today()->addDays(5)->format('Y-m-d'),
                'start_time' => '10:00',
                'end_time'   => '10:30',
                'status'     => 'scheduled',
            ]);

        Http::assertNothingSent();
        $this->assertDatabaseCount('appointment_reminders', 0);
    }

    // ── AppointmentReminderController: destroy ───────────────────────────────

    public function test_admin_can_mark_appointment_reminder_as_completed(): void
    {
        $admin    = User::factory()->create(['role' => 'admin']);
        $client   = User::factory()->create(['role' => 'client']);
        $pet      = Pet::factory()->create(['user_id' => $client->id]);
        $doctor   = User::factory()->create(['role' => 'doctor']);
        $cita     = Appointment::create([
            'doctor_id'  => $doctor->id,
            'pet_id'     => $pet->id,
            'date'       => Carbon::today()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time'   => '09:30:00',
            'status'     => 'scheduled',
        ]);
        $reminder = AppointmentReminder::create([
            'appointment_id' => $cita->id,
            'pet_id'         => $pet->id,
            'user_id'        => $client->id,
            'phone'          => '573000000000',
            'message'        => 'Recordatorio de prueba',
            'status'         => 'sent',
            'sent_at'        => now(),
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('appointment-reminders.destroy', $reminder));

        $response->assertRedirect();
        $this->assertDatabaseHas('appointment_reminders', [
            'id'     => $reminder->id,
            'status' => 'completed',
        ]);
    }

    public function test_doctor_can_mark_appointment_reminder_as_completed(): void
    {
        $doctor   = User::factory()->create(['role' => 'doctor']);
        $client   = User::factory()->create(['role' => 'client']);
        $pet      = Pet::factory()->create(['user_id' => $client->id]);
        $cita     = Appointment::create([
            'doctor_id'  => $doctor->id,
            'pet_id'     => $pet->id,
            'date'       => Carbon::today()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time'   => '09:30:00',
            'status'     => 'scheduled',
        ]);
        $reminder = AppointmentReminder::create([
            'appointment_id' => $cita->id,
            'pet_id'         => $pet->id,
            'user_id'        => $client->id,
            'phone'          => '573000000000',
            'message'        => 'Recordatorio de prueba',
            'status'         => 'sent',
            'sent_at'        => now(),
        ]);

        $response = $this->actingAs($doctor)
            ->delete(route('appointment-reminders.destroy', $reminder));

        $response->assertRedirect();
        $this->assertDatabaseHas('appointment_reminders', [
            'id'     => $reminder->id,
            'status' => 'completed',
        ]);
    }

    public function test_client_cannot_delete_appointment_reminder(): void
    {
        $client   = User::factory()->create(['role' => 'client']);
        $doctor   = User::factory()->create(['role' => 'doctor']);
        $pet      = Pet::factory()->create(['user_id' => $client->id]);
        $cita     = Appointment::create([
            'doctor_id'  => $doctor->id,
            'pet_id'     => $pet->id,
            'date'       => Carbon::today()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time'   => '09:30:00',
            'status'     => 'scheduled',
        ]);
        $reminder = AppointmentReminder::create([
            'appointment_id' => $cita->id,
            'pet_id'         => $pet->id,
            'user_id'        => $client->id,
            'phone'          => '573000000000',
            'message'        => 'Recordatorio de prueba',
            'status'         => 'sent',
            'sent_at'        => now(),
        ]);

        $response = $this->actingAs($client)
            ->delete(route('appointment-reminders.destroy', $reminder));

        $response->assertStatus(403);
        $this->assertDatabaseHas('appointment_reminders', [
            'id'     => $reminder->id,
            'status' => 'sent',
        ]);
    }

    // ── AppointmentReminderController: dismiss ───────────────────────────────

    public function test_client_can_dismiss_their_own_appointment_reminder(): void
    {
        $client   = User::factory()->create(['role' => 'client']);
        $doctor   = User::factory()->create(['role' => 'doctor']);
        $pet      = Pet::factory()->create(['user_id' => $client->id]);
        $cita     = Appointment::create([
            'doctor_id'  => $doctor->id,
            'pet_id'     => $pet->id,
            'date'       => Carbon::today()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time'   => '09:30:00',
            'status'     => 'scheduled',
        ]);
        $reminder = AppointmentReminder::create([
            'appointment_id' => $cita->id,
            'pet_id'         => $pet->id,
            'user_id'        => $client->id,
            'phone'          => '573000000000',
            'message'        => 'Recordatorio de prueba',
            'status'         => 'sent',
            'sent_at'        => now(),
        ]);

        $response = $this->actingAs($client)
            ->patch(route('appointment-reminders.dismiss', $reminder));

        $response->assertRedirect();
        $this->assertDatabaseHas('appointment_reminders', [
            'id'     => $reminder->id,
            'status' => 'dismissed',
        ]);
    }

    public function test_client_cannot_dismiss_another_clients_appointment_reminder(): void
    {
        $clientA  = User::factory()->create(['role' => 'client']);
        $clientB  = User::factory()->create(['role' => 'client']);
        $doctor   = User::factory()->create(['role' => 'doctor']);
        $pet      = Pet::factory()->create(['user_id' => $clientA->id]);
        $cita     = Appointment::create([
            'doctor_id'  => $doctor->id,
            'pet_id'     => $pet->id,
            'date'       => Carbon::today()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time'   => '09:30:00',
            'status'     => 'scheduled',
        ]);
        $reminder = AppointmentReminder::create([
            'appointment_id' => $cita->id,
            'pet_id'         => $pet->id,
            'user_id'        => $clientA->id,
            'phone'          => '573000000000',
            'message'        => 'Recordatorio de prueba',
            'status'         => 'sent',
            'sent_at'        => now(),
        ]);

        $response = $this->actingAs($clientB)
            ->patch(route('appointment-reminders.dismiss', $reminder));

        $response->assertStatus(403);
        $this->assertDatabaseHas('appointment_reminders', [
            'id'     => $reminder->id,
            'status' => 'sent',
        ]);
    }

    // ── Modelo AppointmentReminder ───────────────────────────────────────────

    public function test_appointment_reminder_fillable_fields(): void
    {
        $fillable = (new AppointmentReminder)->getFillable();

        $this->assertContains('appointment_id', $fillable);
        $this->assertContains('pet_id', $fillable);
        $this->assertContains('user_id', $fillable);
        $this->assertContains('phone', $fillable);
        $this->assertContains('message', $fillable);
        $this->assertContains('status', $fillable);
        $this->assertContains('sent_at', $fillable);
    }

    public function test_appointment_reminder_sent_at_cast_to_datetime(): void
    {
        $casts = (new AppointmentReminder)->getCasts();
        $this->assertArrayHasKey('sent_at', $casts);
        $this->assertEquals('datetime', $casts['sent_at']);
    }

    public function test_appointment_reminder_relationships(): void
    {
        $client   = User::factory()->create(['role' => 'client']);
        $doctor   = User::factory()->create(['role' => 'doctor']);
        $pet      = Pet::factory()->create(['user_id' => $client->id]);
        $cita     = Appointment::create([
            'doctor_id'  => $doctor->id,
            'pet_id'     => $pet->id,
            'date'       => Carbon::today()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time'   => '09:30:00',
            'status'     => 'scheduled',
        ]);
        $reminder = AppointmentReminder::create([
            'appointment_id' => $cita->id,
            'pet_id'         => $pet->id,
            'user_id'        => $client->id,
            'phone'          => '573000000000',
            'message'        => 'Recordatorio de prueba',
            'status'         => 'sent',
            'sent_at'        => now(),
        ]);

        $this->assertInstanceOf(Appointment::class, $reminder->appointment);
        $this->assertInstanceOf(Pet::class, $reminder->pet);
        $this->assertInstanceOf(User::class, $reminder->user);
    }

    // ── Comando: mascota sin dueño no envía recordatorio ─────────────────────

public function test_artisan_command_skips_appointment_when_pet_has_no_owner(): void
{
    Config::set('services.whatsapp.driver', 'log');

    $doctor = User::factory()->create(['role' => 'doctor']);
    $client = User::factory()->create(['role' => 'client', 'phone' => '573000000000']);
    $pet    = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Huerfano']);

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
}