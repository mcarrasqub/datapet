<?php

namespace Tests\Feature;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Tests\TestCase;

class MedicalRecordsTest extends TestCase
{
    private const DIAG_GASTRO = 'Gastroenteritis viral';
    private const DIAG_GASTRO_CTRL = 'Gastroenteritis viral - controlada';
    private const TREATMENT_INITIAL = 'Dieta blanda, hidratación oral, metoclopramida 10mg c/8h por 3 días';
    private const TREATMENT_CONTINUE = 'Continuar dieta blanda por 5 días más, seguimiento por vía telefónica';
    private const DERMATITIS = 'Dermatitis alérgica por contacto';
    private const DERMATITIS_TREATMENT = 'Loción calaminada 3 veces al día, evitar alérgenos identificados';
    private const DERMATITIS_REMISSION = 'Dermatitis alérgica por contacto - en remisión';
    private const DERMATITIS_TREATMENT2 = 'Mantener loción calaminada según sea necesario, continuar evitando alérgenos';
    private const NO_APLICA = 'No aplica';
    private User $doctor;
    private Pet $pet;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un doctor autenticado
        $this->doctor = User::factory()->create(['role' => 'doctor']);

        // Crear una mascota asociada a un cliente
        $client = User::factory()->create(['role' => 'client']);
        $this->pet = Pet::factory()->create(['user_id' => $client->id]);
    }

    /**
     * Test del Happy Path: Doctor crea un registro médico con diagnóstico y tratamiento,
     * luego lo edita y verifica que se almacenen correctamente.
     *
     * Criteria covered:
     * - Diagnosis is stored
     * - Treatment plan is visible in record
     * - Data remains accessible for future visits
     */
    public function test_doctor_can_record_diagnosis_and_treatment_in_medical_record(): void
    {
        // Actuar como doctor
        $this->actingAs($this->doctor);

        $treatmentPlan = self::TREATMENT_INITIAL;

        // Datos iniciales del registro médico
        $visitData = [
            'visited_at' => now()->toDateString(),
            'reason' => 'Consulta por molestia gastrointestinal',
            'diagnosis' => self::DIAG_GASTRO,
            'treatment' => $treatmentPlan,
            'notes' => 'Mejoría esperada en 48-72 horas',
        ];

        // Crear el registro médico
        MedicalRecord::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            ...$visitData,
        ]);

        // Verificar que el registro se almacenó correctamente
        $this->assertDatabaseHas('medical_records', [
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'diagnosis' => self::DIAG_GASTRO,
            'treatment' => $treatmentPlan,
        ]);

        // Recuperar el registro
        $record = MedicalRecord::where('pet_id', $this->pet->id)->first();

        // Verificar que el diagnóstico es accesible
        $this->assertEquals(self::DIAG_GASTRO, $record->getDiagnosis());

        // Verificar que el tratamiento es accesible
        $this->assertEquals($treatmentPlan, $record->getTreatment());

        // Actualizar el registro con información adicional
        $updatedData = [
            'visited_at' => $record->visited_at->toDateString(),
            'reason' => $record->reason,
            'diagnosis' => self::DIAG_GASTRO_CTRL,
            'treatment' => self::TREATMENT_CONTINUE,
            'notes' => 'Paciente en recuperación progresiva',
        ];

        $record->update($updatedData);

        // Verificar que la actualización fue exitosa
        $updatedRecord = $record->fresh();
        $this->assertEquals(self::DIAG_GASTRO_CTRL, $updatedRecord->getDiagnosis());
        $this->assertEquals(self::TREATMENT_CONTINUE, $updatedRecord->getTreatment());

        // Verificar que el registro sigue siendo accesible
        $this->assertDatabaseHas('medical_records', [
            'id' => $record->id,
            'diagnosis' => self::DIAG_GASTRO_CTRL,
            'treatment' => self::TREATMENT_CONTINUE,
        ]);
    }

    /**
     * Test de Flujo Alternativo: Doctor accede a un registro médico previo
     * y verifica que el diagnóstico y tratamiento persistan correctamente
     * para futuras visitas y decisiones clínicas.
     *
     * Criteria covered:
     * - Diagnosis is stored (verificado desde visitas anteriores)
     * - Treatment plan is visible in record (accesible para futuras consultas)
     * - Data remains accessible for future visits
     */
    public function test_doctor_can_access_previous_diagnosis_and_treatment_for_future_visits(): void
    {
        // Actuar como doctor
        $this->actingAs($this->doctor);

        // Crear una visita anterior con diagnóstico y tratamiento
        $previousVisit = MedicalRecord::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'visited_at' => now()->subDays(30)->toDateString(),
            'reason' => 'Consulta por alergia cutánea',
            'diagnosis' => self::DERMATITIS,
            'treatment' => self::DERMATITIS_TREATMENT,
            'notes' => 'Revisar en 2 semanas',
        ]);

        // Crear una segunda visita posterior
        $currentVisit = MedicalRecord::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'visited_at' => now()->toDateString(),
            'reason' => 'Seguimiento de dermatitis - mejoría',
            'diagnosis' => self::DERMATITIS_REMISSION,
            'treatment' => self::DERMATITIS_TREATMENT2,
            'notes' => 'Excelente respuesta al tratamiento',
        ]);

        // Obtener todos los registros del historial
        $allRecords = $this->pet->medicalRecords()->orderByDesc('visited_at')->get();

        // Verificar que tenemos ambos registros
        $this->assertCount(2, $allRecords);

        // Acceder al registro previo desde el historial
        $previousRecord = $allRecords->last();

        // Verificar que el diagnóstico anterior es accesible
        $this->assertEquals(self::DERMATITIS, $previousRecord->getDiagnosis());

        // Verificar que el tratamiento anterior es accesible
        $this->assertEquals(self::DERMATITIS_TREATMENT, $previousRecord->getTreatment());

        // Acceder al registro actual
        $currentRecord = $allRecords->first();

        // Verificar que el diagnóstico actual también es accesible
        $this->assertEquals(self::DERMATITIS_REMISSION, $currentRecord->getDiagnosis());

        // Verificar que el tratamiento actual es accesible
        $this->assertEquals(self::DERMATITIS_TREATMENT2, $currentRecord->getTreatment());

        // Verificar que los datos persisten en la base de datos
        $this->assertDatabaseHas('medical_records', [
            'id' => $previousRecord->id,
            'diagnosis' => self::DERMATITIS,
            'treatment' => self::DERMATITIS_TREATMENT,
        ]);

        $this->assertDatabaseHas('medical_records', [
            'id' => $currentRecord->id,
            'diagnosis' => self::DERMATITIS_REMISSION,
            'treatment' => self::DERMATITIS_TREATMENT2,
        ]);
    }

    /**
     * Test negativo: un cliente no debe poder crear registros médicos.
     *
     * Criteria covered:
     * - Access is restricted to doctors/admins
     */
    public function test_client_cannot_create_medical_record(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client);

        $response = $this->post(route('medical_records.store', $this->pet), [
            'visited_at' => now()->toDateString(),
            'reason' => 'Intento no autorizado',
            'diagnosis' => self::NO_APLICA,
            'treatment' => self::NO_APLICA,
            'observation' => self::NO_APLICA,
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('medical_records', [
            'pet_id' => $this->pet->id,
            'reason' => 'Intento no autorizado',
        ]);
    }
}
