<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentsTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_admin_can_create_and_cancel_appointment(): void
    {
        $this->actingAs($this->admin);

        // 1. Creamos la cita
        $appointment = Appointment::create([
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->pet->id,
            'date' => $this->date,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'status' => 'scheduled',
            'reason' => 'Consulta inicial',
        ]);

        // 2. Para actualizar, enviamos TODO el set de datos requeridos
        $response = $this->patch(route('appointments.update', $appointment), [
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->pet->id,
            'date' => $this->date,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'status' => 'canceled', // Solo cambiamos este
            'reason' => 'Consulta inicial',
        ]);

        $response->assertRedirect(route('appointments.index'));
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id, 
            'status' => 'canceled'
        ]);
    }

    public function test_overlapping_appointment_is_rejected(): void
    {
        $this->actingAs($this->admin);

        Appointment::create([
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->pet->id,
            'date' => $this->date,
            'start_time' => '10:00',
            'end_time' => '11:00',
            'status' => 'scheduled',
            'reason' => 'Cita existente',
        ]);

        $response = $this->post(route('appointments.store'), [
            'doctor_id' => $this->doctor->id,
            'pet_id' => $this->pet->id,
            'date' => $this->date,
            'start_time' => '10:30',
            'end_time' => '11:30',
            'status' => 'scheduled',
            'reason' => 'Cita solapada',
        ]);

        $response->assertSessionHasErrors();
    }
}