<?php

namespace Tests\Feature;

use App\Models\ClinicalObservation;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class ClinicalObservationsTest extends TestCase
{
    private User $doctor;
    private Pet $pet;
    private MedicalRecord $medicalRecord;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un doctor autenticado
        $this->doctor = User::factory()->create(['role' => 'doctor']);

        // Crear una mascota asociada a un cliente
        $client = User::factory()->create(['role' => 'client']);
        $this->pet = Pet::factory()->create(['user_id' => $client->id]);

        // Crear un registro médico para la mascota
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
     * Test del Happy Path: Doctor registra una observación clínica durante la consulta,
     * verifica que se guarde correctamente con timestamp y esté ligada a la consulta.
     *
     * Criteria covered:
     * - Observations are saved correctly
     * - Date/time is recorded
     * - Linked to specific consultation
     */
    public function test_doctor_can_record_clinical_observation_with_timestamp(): void
    {
        // Actuar como doctor
        $this->actingAs($this->doctor);

        // Guardar el timestamp inicial
        $observationTime = now();

        // Crear una observación clínica
        $observation = ClinicalObservation::create([
            'medical_record_id' => $this->medicalRecord->id,
            'doctor_id' => $this->doctor->id,
            'observation' => 'Paciente presenta buen estado general, mucosas rosadas, capilares llenos.',
        ]);

        // Verificar que la observación se guardó en la base de datos
        $this->assertDatabaseHas('clinical_observations', [
            'id' => $observation->id,
            'medical_record_id' => $this->medicalRecord->id,
            'doctor_id' => $this->doctor->id,
            'observation' => 'Paciente presenta buen estado general, mucosas rosadas, capilares llenos.',
        ]);

        // Verificar que el timestamp se registró correctamente
        $this->assertNotNull($observation->created_at);
        $this->assertTrue($observation->created_at->isBetween($observationTime->subSeconds(5), now()->addSeconds(5)));

        // Verificar que la observación está ligada a la consulta correcta
        $this->assertEquals($this->medicalRecord->id, $observation->getMedicalRecordId());

        // Verificar que la observación está ligada al doctor correcto
        $this->assertEquals($this->doctor->id, $observation->getDoctorId());

        // Verificar que se puede recuperar desde la relación
        $recordObservations = $this->medicalRecord->observations;
        $this->assertCount(1, $recordObservations);
        $this->assertEquals('Paciente presenta buen estado general, mucosas rosadas, capilares llenos.', $recordObservations->first()->getObservation());
    }

    /**
     * Test de Flujo Alternativo: Doctor registra múltiples observaciones clínicas en diferentes
     * momentos durante la consulta, las edita y verifica que los timestamps y ediciones se
     * registren correctamente, manteniéndolas todas ligadas a la misma consulta.
     *
     * Criteria covered:
     * - Observations are saved correctly (múltiples)
     * - Date/time is recorded (timestamp individual para cada una)
     * - Allow editing (se pueden editar)
     * - Linked to specific consultation
     */
    public function test_doctor_can_record_multiple_observations_with_individual_timestamps_and_editing(): void
    {
        // Actuar como doctor
        $this->actingAs($this->doctor);

        // Primera observación (momento 1)
        $time1 = now();
        $observation1 = ClinicalObservation::create([
            'medical_record_id' => $this->medicalRecord->id,
            'doctor_id' => $this->doctor->id,
            'observation' => 'Se evalúa abdomen: blando, depresible, sin sensibilidad.',
        ]);

        // Esperar un momento para que sea detectable la diferencia de timestamp
        sleep(1);

        // Segunda observación (momento 2)
        $time2 = now();
        $observation2 = ClinicalObservation::create([
            'medical_record_id' => $this->medicalRecord->id,
            'doctor_id' => $this->doctor->id,
            'observation' => 'Se ausculta: ruidos intestinales presentes, sin soplos cardíacos anormales.',
        ]);

        // Esperar un momento más
        sleep(1);

        // Tercera observación (momento 3)
        $time3 = now();
        $observation3 = ClinicalObservation::create([
            'medical_record_id' => $this->medicalRecord->id,
            'doctor_id' => $this->doctor->id,
            'observation' => 'Se toman muestras de sangre para análisis de rutina.',
        ]);

        // Verificar que todas las observaciones están ligadas a la misma consulta
        $allObservations = $this->medicalRecord->observations()->orderBy('created_at')->get();
        $this->assertCount(3, $allObservations);

        // Verificar que cada observación tiene su propio timestamp y no son nulas
        $this->assertNotNull($observation1->created_at);
        $this->assertNotNull($observation2->created_at);
        $this->assertNotNull($observation3->created_at);

        // Verificar que los timestamps son progresivos
        $this->assertTrue($observation1->created_at->isBefore($observation2->created_at));
        $this->assertTrue($observation2->created_at->isBefore($observation3->created_at));

        // Editar la primera observación
        $updatedObservationText = 'Se evalúa abdomen: blando, depresible, sin sensibilidad. (Revisión: sin cambios respecto a visita anterior)';
        $observation1->update(['observation' => $updatedObservationText]);

        // Verificar que la edición se guardó
        $updatedObservation1 = ClinicalObservation::find($observation1->id);
        $this->assertEquals($updatedObservationText, $updatedObservation1->getObservation());

        // Verificar que el timestamp de edición (updated_at) se actualizó
        $this->assertTrue($updatedObservation1->updated_at->isAfter($updatedObservation1->created_at));

        // Verificar que el timestamp de creación no cambió
        $this->assertEquals($observation1->created_at->timestamp, $updatedObservation1->created_at->timestamp);

        // Verificar que todas las observaciones siguen ligadas a la consulta correcta
        $this->assertDatabaseHas('clinical_observations', [
            'id' => $observation1->id,
            'medical_record_id' => $this->medicalRecord->id,
            'observation' => $updatedObservationText,
        ]);

        $this->assertDatabaseHas('clinical_observations', [
            'id' => $observation2->id,
            'medical_record_id' => $this->medicalRecord->id,
        ]);

        $this->assertDatabaseHas('clinical_observations', [
            'id' => $observation3->id,
            'medical_record_id' => $this->medicalRecord->id,
        ]);
    }

    /**
     * Test del Happy Path HTTP: Doctor crea una observación clínica desde la consulta
     * y verifica que quede asociada al expediente y con timestamp.
     */
    public function test_doctor_can_create_clinical_observation_from_medical_record_view(): void
    {
        $this->actingAs($this->doctor);

        $response = $this->post(route('clinical_observations.store', $this->medicalRecord), [
            'observation' => 'Paciente se mantiene estable, con buena evolución clínica.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Observación clínica registrada con éxito.');

        $this->assertDatabaseHas('clinical_observations', [
            'medical_record_id' => $this->medicalRecord->id,
            'doctor_id' => $this->doctor->id,
            'observation' => 'Paciente se mantiene estable, con buena evolución clínica.',
        ]);

        $this->assertCount(1, $this->medicalRecord->fresh()->observations);
    }

    /**
     * Test de Flujo Alternativo HTTP: Doctor edita una observación previa y confirma
     * que sigue vinculada a la misma consulta y conserva la fecha de creación.
     */
    public function test_doctor_can_edit_existing_clinical_observation(): void
    {
        $this->actingAs($this->doctor);

        $observation = ClinicalObservation::create([
            'medical_record_id' => $this->medicalRecord->id,
            'doctor_id' => $this->doctor->id,
            'observation' => 'Observación inicial de control.',
        ]);

        $response = $this->put(route('clinical_observations.update', $observation), [
            'observation' => 'Observación inicial de control. Ajustada tras revisión física.',
        ]);

        $response->assertRedirect(route('medical_records.show', $this->pet));
        $response->assertSessionHas('success', 'Observación clínica actualizada con éxito.');

        $this->assertDatabaseHas('clinical_observations', [
            'id' => $observation->id,
            'medical_record_id' => $this->medicalRecord->id,
            'doctor_id' => $this->doctor->id,
            'observation' => 'Observación inicial de control. Ajustada tras revisión física.',
        ]);
    }

    /**
     * Test alternativo adicional: Doctor elimina una observación y confirma que deja
     * de existir sin afectar el expediente principal.
     */
    public function test_doctor_can_delete_clinical_observation(): void
    {
        $this->actingAs($this->doctor);

        $observation = ClinicalObservation::create([
            'medical_record_id' => $this->medicalRecord->id,
            'doctor_id' => $this->doctor->id,
            'observation' => 'Observación para eliminar.',
        ]);

        $response = $this->delete(route('clinical_observations.destroy', $observation));

        $response->assertRedirect(route('medical_records.show', $this->pet));
        $response->assertSessionHas('success', 'Observación clínica eliminada con éxito.');

        $this->assertDatabaseMissing('clinical_observations', [
            'id' => $observation->id,
        ]);

        $this->assertTrue($this->medicalRecord->fresh()->observations->isEmpty());
    }

    /**
     * Test negativo: si el doctor intenta guardar una observación vacía,
     * el sistema debe rechazarla y no crear ningún registro.
     */
    public function test_doctor_cannot_create_clinical_observation_without_text(): void
    {
        $this->actingAs($this->doctor);

        $response = $this->post(route('clinical_observations.store', $this->medicalRecord), [
            'observation' => '',
        ]);

        $response->assertSessionHasErrors(['observation']);

        $this->assertDatabaseMissing('clinical_observations', [
            'medical_record_id' => $this->medicalRecord->id,
            'doctor_id' => $this->doctor->id,
        ]);
    }
}
