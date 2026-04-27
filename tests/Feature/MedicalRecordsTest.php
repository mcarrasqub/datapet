<?php

namespace Tests\Feature;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Tests\TestCase;

class MedicalRecordsTest extends TestCase
{
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

        // Datos iniciales del registro médico
        $visitData = [
            'visited_at' => now()->toDateString(),
            'reason' => 'Consulta por molestia gastrointestinal',
            'diagnosis' => 'Gastroenteritis viral',
            'treatment' => 'Dieta blanda, hidratación oral, metoclopramida 10mg c/8h por 3 días',
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
            'diagnosis' => 'Gastroenteritis viral',
            'treatment' => 'Dieta blanda, hidratación oral, metoclopramida 10mg c/8h por 3 días',
        ]);

        // Recuperar el registro
        $record = MedicalRecord::where('pet_id', $this->pet->id)->first();

        // Verificar que el diagnóstico es accesible
        $this->assertEquals('Gastroenteritis viral', $record->getDiagnosis());

        // Verificar que el tratamiento es accesible
        $this->assertEquals('Dieta blanda, hidratación oral, metoclopramida 10mg c/8h por 3 días', $record->getTreatment());

        // Actualizar el registro con información adicional
        $updatedData = [
            'visited_at' => $record->visited_at->toDateString(),
            'reason' => $record->reason,
            'diagnosis' => 'Gastroenteritis viral - controlada',
            'treatment' => 'Continuar dieta blanda por 5 días más, seguimiento por vía telefónica',
            'notes' => 'Paciente en recuperación progresiva',
        ];

        $record->update($updatedData);

        // Verificar que la actualización fue exitosa
        $updatedRecord = $record->fresh();
        $this->assertEquals('Gastroenteritis viral - controlada', $updatedRecord->getDiagnosis());
        $this->assertEquals('Continuar dieta blanda por 5 días más, seguimiento por vía telefónica', $updatedRecord->getTreatment());

        // Verificar que el registro sigue siendo accesible
        $this->assertDatabaseHas('medical_records', [
            'id' => $record->id,
            'diagnosis' => 'Gastroenteritis viral - controlada',
            'treatment' => 'Continuar dieta blanda por 5 días más, seguimiento por vía telefónica',
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
            'diagnosis' => 'Dermatitis alérgica por contacto',
            'treatment' => 'Loción calaminada 3 veces al día, evitar alérgenos identificados',
            'notes' => 'Revisar en 2 semanas',
        ]);

        // Crear una segunda visita posterior
        $currentVisit = MedicalRecord::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'visited_at' => now()->toDateString(),
            'reason' => 'Seguimiento de dermatitis - mejoría',
            'diagnosis' => 'Dermatitis alérgica por contacto - en remisión',
            'treatment' => 'Mantener loción calaminada según sea necesario, continuar evitando alérgenos',
            'notes' => 'Excelente respuesta al tratamiento',
        ]);

        // Obtener todos los registros del historial
        $allRecords = $this->pet->medicalRecords()->orderByDesc('visited_at')->get();

        // Verificar que tenemos ambos registros
        $this->assertCount(2, $allRecords);

        // Acceder al registro previo desde el historial
        $previousRecord = $allRecords->last();

        // Verificar que el diagnóstico anterior es accesible
        $this->assertEquals('Dermatitis alérgica por contacto', $previousRecord->getDiagnosis());

        // Verificar que el tratamiento anterior es accesible
        $this->assertEquals('Loción calaminada 3 veces al día, evitar alérgenos identificados', $previousRecord->getTreatment());

        // Acceder al registro actual
        $currentRecord = $allRecords->first();

        // Verificar que el diagnóstico actual también es accesible
        $this->assertEquals('Dermatitis alérgica por contacto - en remisión', $currentRecord->getDiagnosis());

        // Verificar que el tratamiento actual es accesible
        $this->assertEquals('Mantener loción calaminada según sea necesario, continuar evitando alérgenos', $currentRecord->getTreatment());

        // Verificar que los datos persisten en la base de datos
        $this->assertDatabaseHas('medical_records', [
            'id' => $previousRecord->id,
            'diagnosis' => 'Dermatitis alérgica por contacto',
            'treatment' => 'Loción calaminada 3 veces al día, evitar alérgenos identificados',
        ]);

        $this->assertDatabaseHas('medical_records', [
            'id' => $currentRecord->id,
            'diagnosis' => 'Dermatitis alérgica por contacto - en remisión',
            'treatment' => 'Mantener loción calaminada según sea necesario, continuar evitando alérgenos',
        ]);
    }
}
