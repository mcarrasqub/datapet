<?php

namespace Tests\Feature;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalRecordsTest extends TestCase
{
    use RefreshDatabase;

    private User $doctor;
    private Pet $pet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->doctor = User::factory()->create(['role' => 'doctor']);
        $client = User::factory()->create(['role' => 'client']);
        $this->pet = Pet::factory()->create(['user_id' => $client->id]);
    }

    /**
     * Happy Path: Crear vía Controlador para subir cobertura.
     */
    public function test_doctor_can_create_medical_record_via_controller(): void
    {
        $this->actingAs($this->doctor);

        $visitData = [
            'visited_at' => now()->toDateString(),
            'reason' => 'Consulta rutinaria',
            'diagnosis' => 'Sano',
            'treatment' => 'Ninguno',
        ];

        // LLAMADA AL CONTROLADOR (Esto sube el coverage)
        $response = $this->post(route('medical_records.store', $this->pet), $visitData);

        $response->assertRedirect();
        $this->assertDatabaseHas('medical_records', ['diagnosis' => 'Sano']);
    }

    /**
     * Flujo Alternativo: Validación fallida (Pinta de verde las validaciones).
     */
    public function test_cannot_create_record_without_diagnosis(): void
    {
        $this->actingAs($this->doctor);

        $response = $this->post(route('medical_records.store', $this->pet), [
            'diagnosis' => '', // Error provocado
        ]);

        $response->assertSessionHasErrors('diagnosis');
    }
}