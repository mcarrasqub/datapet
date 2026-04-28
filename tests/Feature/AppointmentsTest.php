<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use Tests\TestCase;

class AppointmentsTest extends TestCase
{
    private const TIME_0900 = '09:00';
    private const TIME_1000 = '10:00';
    private User $admin;
    private User $doctor;
    private Pet $pet;
    private string $date;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->doctor = User::factory()->create(['role' => 'doctor']);

        $client = User::factory()->create(['role' => 'client']);
        $this->pet = Pet::factory()->create(['user_id' => $client->id]);
        $this->date = now()->addDay()->format('Y-m-d');
    }

    /**
     * Happy path: el admin crea una cita y luego la actualiza a cancelada.
     *
     * Criteria covered:
     * - Appointment linked to pet and doctor
     * - Status is updated (scheduled/canceled)
     */
    public function test_admin_can_create_and_cancel_appointment(): void
    {
        $this->actingAs($this->admin);

        $createResponse = $this->post(route('appointments.store'), [
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->pet->id,
            'date' => $this->date,
            'start_time' => self::TIME_0900,
            'end_time' => self::TIME_1000,
            'status' => 'scheduled',
            'reason' => 'Consulta de control',
        ]);

        $createResponse->assertRedirect(route('appointments.index'));
        $createResponse->assertSessionHas('success', 'Cita creada exitosamente.');

        $appointment = Appointment::query()->first();
        $this->assertNotNull($appointment);
        $this->assertDatabaseHas('appointments', [
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->pet->id,
            'date' => $this->date,
            'start_time' => self::TIME_0900,
            'end_time' => self::TIME_1000,
            'status' => 'scheduled',
        ]);

        $updateResponse = $this->put(route('appointments.update', $appointment), [
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->pet->id,
            'date' => $this->date,
            'start_time' => self::TIME_0900,
            'end_time' => self::TIME_1000,
            'status' => 'canceled',
            'reason' => 'Consulta cancelada por el cliente',
        ]);

        $updateResponse->assertRedirect(route('appointments.index'));
        $updateResponse->assertSessionHas('success', 'Cita actualizada exitosamente.');

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->pet->id,
            'status' => 'canceled',
            'reason' => 'Consulta cancelada por el cliente',
        ]);
    }

    /**
     * Flujo alternativo: no se debe permitir un solapamiento incorrecto del horario.
     *
     * Criteria covered:
     * - Appointment cannot overlap incorrectly
     */
    public function test_overlapping_appointment_is_rejected(): void
    {
        $this->actingAs($this->admin);

        Appointment::create([
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->pet->id,
            'date' => $this->date,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'status' => 'scheduled',
            'reason' => 'Primera cita',
        ]);

        $response = $this->post(route('appointments.store'), [
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->pet->id,
            'date' => $this->date,
            'start_time' => '09:30',
            'end_time' => '10:30',
            'status' => 'scheduled',
            'reason' => 'Cita solapada',
        ]);

        $response->assertSessionHasErrors(['start_time']);
        $this->assertDatabaseMissing('appointments', [
            'reason' => 'Cita solapada',
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->pet->id,
        ]);
    }

    /**
     * Test negativo adicional: una cita cancelada no debe bloquear una nueva cita.
     */
    public function test_canceled_appointment_does_not_block_new_scheduled_appointment(): void
    {
        $this->actingAs($this->admin);

        Appointment::create([
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->pet->id,
            'date' => $this->date,
            'start_time' => '11:00',
            'end_time' => '12:00',
            'status' => 'canceled',
            'reason' => 'Cita cancelada previamente',
        ]);

        $response = $this->post(route('appointments.store'), [
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->pet->id,
            'date' => $this->date,
            'start_time' => '11:15',
            'end_time' => '11:45',
            'status' => 'scheduled',
            'reason' => 'Nueva cita válida',
        ]);

        $response->assertRedirect(route('appointments.index'));
        $response->assertSessionHas('success', 'Cita creada exitosamente.');

        $this->assertDatabaseHas('appointments', [
            'reason' => 'Nueva cita válida',
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->pet->id,
            'status' => 'scheduled',
        ]);
    }
}
