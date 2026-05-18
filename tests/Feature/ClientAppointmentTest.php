<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientAppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_appointments(): void
    {
        $response = $this->get(route('appointments.client_index'));
        $response->assertRedirect(route('login'));
    }

    public function test_client_can_view_their_appointments(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $pet = Pet::factory()->create([
            'user_id' => $client->id,
        ]);

        $appointment = Appointment::create([
            'doctor_id' => $doctor->id,
            'pet_id' => $pet->id,
            'status' => 'scheduled',
            'date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '10:30',
        ]);

        $response = $this->actingAs($client)->get(route('appointments.client_index'));

        $response->assertStatus(200);
        $response->assertViewIs('appointments.client_index');
        $response->assertSee($pet->getName());
        $response->assertSee('Programada');
    }

    public function test_client_cannot_see_other_clients_appointments(): void
    {
        $client1 = User::factory()->create(['role' => 'client']);
        $client2 = User::factory()->create(['role' => 'client']);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $pet2 = Pet::factory()->create([
            'user_id' => $client2->id,
            'name' => 'Other Pet Name',
        ]);

        Appointment::create([
            'doctor_id' => $doctor->id,
            'pet_id' => $pet2->id,
            'status' => 'scheduled',
            'date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '10:30',
        ]);

        $response = $this->actingAs($client1)->get(route('appointments.client_index'));

        $response->assertStatus(200);
        $response->assertDontSee('Other Pet Name');
    }
}
