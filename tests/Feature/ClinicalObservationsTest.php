<?php

namespace Tests\Feature;

use App\Models\ClinicalObservation;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicalObservationsTest extends TestCase
{
    use RefreshDatabase; 

    private const OBS_GOOD_STATE = 'Paciente presenta buen estado general, mucosas rosadas, capilares llenos.';
    private User $doctor;
    private Pet $pet;
    private MedicalRecord $medicalRecord;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Crear el entorno (Setup)
        $this->doctor = User::factory()->create(['role' => 'doctor']);
        $client = User::factory()->create(['role' => 'client']);
        $this->pet = Pet::factory()->create(['user_id' => $client->id]);

        $this->medicalRecord = MedicalRecord::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'visited_at' => now(),
            'reason' => 'Consulta general',
            'diagnosis' => 'Chequeo rutinario',
            'treatment' => 'Sin tratamiento requerido',
        ]);
    }

    /**
     * HU 9 - Happy Path: Registro con Timestamp.
     * Al usar $this->post, cubrimos la lógica del Controlador.
     */
    public function test_doctor_can_record_clinical_observation_with_timestamp(): void
    {
        $this->actingAs($this->doctor);
        $observationTime = now();

        $response = $this->post(route('clinical_observations.store', $this->medicalRecord), [
            'observation' => self::OBS_GOOD_STATE,
        ]);

        $response->assertRedirect();
        
        // Verificamos persistencia
        $this->assertDatabaseHas('clinical_observations', [
            'medical_record_id' => $this->medicalRecord->id,
            'doctor_id' => $this->doctor->id,
            'observation' => self::OBS_GOOD_STATE,
        ]);

        $observation = ClinicalObservation::first();
        $this->assertNotNull($observation->created_at);
        // Verifica que el ID del MedicalRecord coincida (coherencia)
        $this->assertEquals($this->medicalRecord->id, $observation->medical_record_id);
    }

    /**
     * HU 9 - Flujo Alternativo: Múltiples observaciones y Edición.
     * Cubre la persistencia progresiva y la actualización de datos.
     */
    public function test_doctor_can_record_multiple_observations_and_edit_them(): void
    {
        $this->actingAs($this->doctor);

        // Crear dos observaciones vía HTTP
        $this->post(route('clinical_observations.store', $this->medicalRecord), ['observation' => 'Obs 1']);
        sleep(1); // Para diferenciar timestamps
        $this->post(route('clinical_observations.store', $this->medicalRecord), ['observation' => 'Obs 2']);

        $this->assertCount(2, ClinicalObservation::all());

        // Editar la primera
        $observation = ClinicalObservation::first();
        $updatedText = 'Obs 1 Editada';
        
        $response = $this->put(route('clinical_observations.update', $observation), [
            'observation' => $updatedText,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals($updatedText, $observation->fresh()->observation);
    }

    /**
     * HU 9 - Flujo Alternativo: Eliminación.
     * Verifica que se borre la observación sin afectar el expediente principal.
     */
    public function test_doctor_can_delete_clinical_observation(): void
    {
        $this->actingAs($this->doctor);

        $observation = ClinicalObservation::create([
            'medical_record_id' => $this->medicalRecord->id,
            'doctor_id' => $this->doctor->id,
            'observation' => 'Para eliminar',
        ]);

        $response = $this->delete(route('clinical_observations.destroy', $observation));

        $response->assertRedirect();
        $this->assertDatabaseMissing('clinical_observations', ['id' => $observation->id]);
    }

    /**
     * Test Negativo (Muy importante para Coverage): Validación fallida.
     * Esto "pinta de verde" las líneas de validación de tu controlador.
     */
    public function test_doctor_cannot_create_clinical_observation_without_text(): void
    {
        $this->actingAs($this->doctor);

        $response = $this->post(route('clinical_observations.store', $this->medicalRecord), [
            'observation' => '', // Campo vacío para forzar error
        ]);

        $response->assertSessionHasErrors(['observation']);
        $this->assertDatabaseCount('clinical_observations', 0);
    }

    /**
     * Test de Seguridad: Un cliente no puede borrar observaciones.
     */
    public function test_client_cannot_delete_observations(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $observation = ClinicalObservation::create([
            'medical_record_id' => $this->medicalRecord->id,
            'doctor_id' => $this->doctor->id,
            'observation' => 'Obs sensible',
        ]);

        $response = $this->actingAs($client)->delete(route('clinical_observations.destroy', $observation));
        
        $response->assertStatus(403); // Forbidden
        $this->assertDatabaseHas('clinical_observations', ['id' => $observation->id]);
    }
}