<?php

namespace Tests\Feature;

use App\Models\MedicalOrder;
use App\Models\Pet;
use App\Models\User;
use App\Models\VaccinationReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorOrderNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_medical_order_generates_notification_for_owner(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor', 'name' => 'John Doe']);
        $client = User::factory()->create(['role' => 'client', 'phone' => '123456789']);
        $pet = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Fido']);

        $response = $this->actingAs($doctor)->post(route('orders.store', $pet), [
            'order_date' => now()->toDateString(),
            'order_type' => 'Laboratorio',
            'description' => 'Hemograma de rutina',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('medical_orders', [
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'order_type' => 'Laboratorio',
            'description' => 'Hemograma de rutina',
        ]);

        $order = MedicalOrder::first();

        // Verify the notification record was created in vaccination_reminders
        $this->assertDatabaseHas('vaccination_reminders', [
            'vaccination_id' => null,
            'medical_order_id' => $order->id,
            'pet_id' => $pet->id,
            'user_id' => $client->id,
            'status' => 'sent',
        ]);

        $reminder = VaccinationReminder::first();
        $this->assertNotNull($reminder->medicalOrder);
        $this->assertEquals($order->id, $reminder->medicalOrder->id);
        $this->assertStringContainsString('John Doe', $reminder->message);
        $this->assertStringContainsString('Laboratorio', $reminder->message);
        $this->assertStringContainsString('Fido', $reminder->message);
    }

    public function test_client_can_view_order_notification_in_notifications_page(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor', 'name' => 'John Doe']);
        $client = User::factory()->create(['role' => 'client', 'phone' => '123456789']);
        $pet = Pet::factory()->create(['user_id' => $client->id, 'name' => 'Fido']);

        $order = MedicalOrder::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'order_date' => now()->toDateString(),
            'order_type' => 'Laboratorio',
            'description' => 'Hemograma de rutina',
            'status' => 'pending',
        ]);

        $reminder = VaccinationReminder::create([
            'vaccination_id' => null,
            'medical_order_id' => $order->id,
            'pet_id' => $pet->id,
            'user_id' => $client->id,
            'phone' => $client->phone,
            'message' => 'El Dr. John Doe ha emitido una nueva orden clínica de tipo Laboratorio para tu mascota Fido.',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $response = $this->actingAs($client)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Centro de Notificaciones');
        $response->assertSee('Nueva Orden Clínica para Fido');
        $response->assertSee('El Dr. John Doe ha emitido una nueva orden clínica de tipo Laboratorio para tu mascota Fido.');
        $response->assertSee(route('pets.exams', ['pet_id' => $pet->id]));
    }

    public function test_client_can_dismiss_order_notification(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $client = User::factory()->create(['role' => 'client']);
        $pet = Pet::factory()->create(['user_id' => $client->id]);

        $order = MedicalOrder::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'order_date' => now()->toDateString(),
            'order_type' => 'Laboratorio',
            'description' => 'Hemograma de rutina',
            'status' => 'pending',
        ]);

        $reminder = VaccinationReminder::create([
            'vaccination_id' => null,
            'medical_order_id' => $order->id,
            'pet_id' => $pet->id,
            'user_id' => $client->id,
            'phone' => '123',
            'message' => 'test',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $this->assertDatabaseHas('vaccination_reminders', [
            'id' => $reminder->id,
            'status' => 'sent',
        ]);

        $response = $this->actingAs($client)->patch(route('reminders.dismiss', $reminder));

        $response->assertRedirect();
        $this->assertDatabaseHas('vaccination_reminders', [
            'id' => $reminder->id,
            'status' => 'dismissed',
        ]);
    }

    public function test_deleting_medical_order_deletes_notification(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $client = User::factory()->create(['role' => 'client']);
        $pet = Pet::factory()->create(['user_id' => $client->id]);

        $order = MedicalOrder::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'order_date' => now()->toDateString(),
            'order_type' => 'Laboratorio',
            'description' => 'Hemograma de rutina',
            'status' => 'pending',
        ]);

        $reminder = VaccinationReminder::create([
            'vaccination_id' => null,
            'medical_order_id' => $order->id,
            'pet_id' => $pet->id,
            'user_id' => $client->id,
            'phone' => '123',
            'message' => 'test',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $this->assertDatabaseHas('vaccination_reminders', ['id' => $reminder->id]);

        // Delete order via Doctor Controller action
        $response = $this->actingAs($doctor)->delete(route('orders.destroy', $order));

        $response->assertRedirect();

        $this->assertDatabaseMissing('medical_orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('vaccination_reminders', ['id' => $reminder->id]);
    }
}
