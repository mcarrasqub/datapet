<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\User;
use App\Models\Vaccination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class VaccinationReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_artisan_command_detects_upcoming_vaccines_using_log_driver(): void
    {
        Config::set('services.whatsapp.driver', 'log');

        Log::shouldReceive('info')->once()->withArgs(function ($message) {
            return str_contains($message, '[WHATSAPP RECORDATORIO]') && str_contains($message, 'Noodle') && str_contains($message, 'Rabia');
        });

        $client = User::factory()->create([
            'role' => 'client',
            'phone' => '3216549870',
            'name' => 'Mariana',
        ]);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $pet = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Noodle']);

        $vaccination = Vaccination::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'vaccine_type' => 'Rabia',
            'vaccinated_at' => now()->subMonths(11)->toDateString(),
            'next_due_date' => now()->addDays(1)->toDateString(),
        ]);

        $this->artisan('pet:send-vaccine-reminders')
            ->expectsOutput('Escaneando vacunas próximas a vencer...')
            ->expectsOutputToContain('Se procesaron 1 alertas.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('vaccination_reminders', [
            'vaccination_id' => $vaccination->id,
            'pet_id' => $pet->id,
            'user_id' => $client->id,
            'status' => 'sent',
        ]);
    }

    public function test_artisan_command_sends_real_whatsapp_using_ultramsg_driver(): void
    {
        Config::set('services.whatsapp.driver', 'ultramsg');
        Config::set('services.whatsapp.ultramsg.instance', 'inst123');
        Config::set('services.whatsapp.ultramsg.token', 'tok456');

        Http::fake([
            'api.ultramsg.com/*' => Http::response(['success' => true], 200),
        ]);

        $client = User::factory()->create([
            'role' => 'client',
            'phone' => '3216549870',
            'name' => 'Mariana',
        ]);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $pet = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Noodle']);

        Vaccination::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'vaccine_type' => 'Rabia',
            'vaccinated_at' => now()->subMonths(11)->toDateString(),
            'next_due_date' => now()->addDays(1)->toDateString(),
        ]);

        $this->artisan('pet:send-vaccine-reminders')->assertExitCode(0);

        // Verify the HTTP request was sent to UltraMsg API
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.ultramsg.com/inst123/messages/chat' &&
                $request['token'] === 'tok456' &&
                $request['to'] === '3216549870' &&
                str_contains($request['body'], 'Noodle') &&
                str_contains($request['body'], 'Rabia');
        });

        $this->assertDatabaseHas('vaccination_reminders', [
            'phone' => '3216549870',
            'status' => 'sent',
        ]);
    }

    public function test_automatic_reminder_trigger_on_client_dashboard_load(): void
    {
        Config::set('services.whatsapp.driver', 'log');

        $client = User::factory()->create(['role' => 'client', 'phone' => '3216549870']);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $pet = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Noodle']);

        $vaccination = Vaccination::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'vaccine_type' => 'Rabia',
            'vaccinated_at' => now()->subMonths(11)->toDateString(),
            'next_due_date' => now()->addDays(1)->toDateString(),
        ]);

        // Prior to loading, no reminders should exist
        $this->assertDatabaseCount('vaccination_reminders', 0);

        // Client loads their dashboard home page - triggers automatic scan!
        $response = $this->actingAs($client)->get(route('home.index'));

        $response->assertStatus(200);
        $response->assertSee('notificación pendiente');
        $response->assertSee('Ver Notificaciones');

        // Assert that the client DOES NOT see the WhatsApp action button or link
        $response->assertDontSee('Enviar Recordatorio');
        $response->assertDontSee('wa.me');

        // Verify reminder was created in the DB automatically
        $this->assertDatabaseHas('vaccination_reminders', [
            'vaccination_id' => $vaccination->id,
            'pet_id' => $pet->id,
            'user_id' => $client->id,
            'status' => 'sent',
        ]);
    }

    public function test_automatic_reminder_trigger_on_admin_dashboard_load(): void
    {
        Config::set('services.whatsapp.driver', 'log');

        $client = User::factory()->create(['role' => 'client', 'phone' => '3216549870']);
        $admin = User::factory()->create(['role' => 'admin']);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $pet = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Noodle']);

        $vaccination = Vaccination::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'vaccine_type' => 'Rabia',
            'vaccinated_at' => now()->subMonths(11)->toDateString(),
            'next_due_date' => now()->addDays(1)->toDateString(),
        ]);

        $this->assertDatabaseCount('vaccination_reminders', 0);

        // Admin loads their dashboard page - triggers automatic scan!
        $response = $this->actingAs($admin)->get(route('dashboard.index'));

        $response->assertStatus(200);
        $response->assertSee('Recordatorios de Vacunación Próximos');
        $response->assertSee('Noodle');
        $response->assertSee('Enviar');

        $this->assertDatabaseHas('vaccination_reminders', [
            'vaccination_id' => $vaccination->id,
            'pet_id' => $pet->id,
            'status' => 'sent',
        ]);
    }

    public function test_doctor_dashboard_does_not_see_whatsapp_reminders(): void
    {
        Config::set('services.whatsapp.driver', 'log');

        $client = User::factory()->create(['role' => 'client', 'phone' => '3216549870']);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $pet = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Noodle']);

        Vaccination::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'vaccine_type' => 'Rabia',
            'vaccinated_at' => now()->subMonths(11)->toDateString(),
            'next_due_date' => now()->addDays(1)->toDateString(),
        ]);

        // Doctor loads their dashboard page
        $response = $this->actingAs($doctor)->get(route('dashboard.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Recordatorios de Vacunación Próximos');
        $response->assertDontSee('Enviar');
    }

    public function test_artisan_command_does_not_duplicate_reminders(): void
    {
        Config::set('services.whatsapp.driver', 'log');

        $client = User::factory()->create(['role' => 'client', 'phone' => '3216549870']);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $pet = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Noodle']);

        Vaccination::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'vaccine_type' => 'Rabia',
            'vaccinated_at' => now()->subMonths(11)->toDateString(),
            'next_due_date' => now()->addDays(1)->toDateString(),
        ]);

        $this->artisan('pet:send-vaccine-reminders')->assertExitCode(0);
        $this->assertDatabaseCount('vaccination_reminders', 1);

        $this->artisan('pet:send-vaccine-reminders')
            ->expectsOutputToContain('Se procesaron 0 alertas.')
            ->assertExitCode(0);

        $this->assertDatabaseCount('vaccination_reminders', 1);
    }

    public function test_admin_can_complete_and_delete_vaccination_reminder(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['role' => 'client']);
        $pet = Pet::factory()->create(['user_id' => $client->id]);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $vaccination = Vaccination::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'vaccine_type' => 'Rabia',
            'vaccinated_at' => now()->subMonths(11)->toDateString(),
            'next_due_date' => now()->addDays(1)->toDateString(),
        ]);

        $reminder = \App\Models\VaccinationReminder::create([
            'vaccination_id' => $vaccination->id,
            'pet_id' => $pet->id,
            'user_id' => $client->id,
            'phone' => '123456',
            'message' => 'Test message',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $this->assertDatabaseHas('vaccination_reminders', ['id' => $reminder->id]);

        $response = $this->actingAs($admin)
            ->delete(route('reminders.destroy', $reminder));

        $response->assertRedirect();
        $this->assertDatabaseHas('vaccination_reminders', [
            'id' => $reminder->id,
            'status' => 'completed',
        ]);
    }

    public function test_client_cannot_delete_vaccination_reminder(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $pet = Pet::factory()->create(['user_id' => $client->id]);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $vaccination = Vaccination::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'vaccine_type' => 'Rabia',
            'vaccinated_at' => now()->subMonths(11)->toDateString(),
            'next_due_date' => now()->addDays(1)->toDateString(),
        ]);

        $reminder = \App\Models\VaccinationReminder::create([
            'vaccination_id' => $vaccination->id,
            'pet_id' => $pet->id,
            'user_id' => $client->id,
            'phone' => '123456',
            'message' => 'Test message',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $response = $this->actingAs($client)
            ->delete(route('reminders.destroy', $reminder));

        $response->assertStatus(403);
        $this->assertDatabaseHas('vaccination_reminders', ['id' => $reminder->id]);
    }

    public function test_client_can_dismiss_their_own_vaccination_reminder(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $pet = Pet::factory()->create(['user_id' => $client->id]);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $vaccination = Vaccination::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'vaccine_type' => 'Rabia',
            'vaccinated_at' => now()->subMonths(11)->toDateString(),
            'next_due_date' => now()->addDays(1)->toDateString(),
        ]);

        $reminder = \App\Models\VaccinationReminder::create([
            'vaccination_id' => $vaccination->id,
            'pet_id' => $pet->id,
            'user_id' => $client->id,
            'phone' => '123456',
            'message' => 'Test message',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $this->assertDatabaseHas('vaccination_reminders', ['id' => $reminder->id, 'status' => 'sent']);

        $response = $this->actingAs($client)
            ->patch(route('reminders.dismiss', $reminder));

        $response->assertRedirect();
        $this->assertDatabaseHas('vaccination_reminders', [
            'id' => $reminder->id,
            'status' => 'dismissed',
        ]);
    }

    public function test_client_cannot_dismiss_other_clients_vaccination_reminder(): void
    {
        $clientA = User::factory()->create(['role' => 'client']);
        $clientB = User::factory()->create(['role' => 'client']);
        $pet = Pet::factory()->create(['user_id' => $clientA->id]);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $vaccination = Vaccination::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'vaccine_type' => 'Rabia',
            'vaccinated_at' => now()->subMonths(11)->toDateString(),
            'next_due_date' => now()->addDays(1)->toDateString(),
        ]);

        $reminder = \App\Models\VaccinationReminder::create([
            'vaccination_id' => $vaccination->id,
            'pet_id' => $pet->id,
            'user_id' => $clientA->id,
            'phone' => '123456',
            'message' => 'Test message',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $response = $this->actingAs($clientB)
            ->patch(route('reminders.dismiss', $reminder));

        $response->assertStatus(403);
        $this->assertDatabaseHas('vaccination_reminders', [
            'id' => $reminder->id,
            'status' => 'sent',
        ]);
    }

    public function test_client_can_view_notifications_page(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $pet = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Noodle']);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $vaccination = Vaccination::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'vaccine_type' => 'Rabia',
            'vaccinated_at' => now()->subMonths(11)->toDateString(),
            'next_due_date' => now()->addDays(1)->toDateString(),
        ]);

        $reminder = \App\Models\VaccinationReminder::create([
            'vaccination_id' => $vaccination->id,
            'pet_id' => $pet->id,
            'user_id' => $client->id,
            'phone' => '123456',
            'message' => 'Test message',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $response = $this->actingAs($client)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Centro de Notificaciones');
        $response->assertSee('Alerta de Vacuna para Noodle');
        $response->assertSee('Rabia');
    }
}
