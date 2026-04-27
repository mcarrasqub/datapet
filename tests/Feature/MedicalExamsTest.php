<?php

namespace Tests\Feature;

use App\Models\MedicalExam;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MedicalExamsTest extends TestCase
{
    private User $doctor;
    private User $client;
    private Pet $pet;
    private MedicalRecord $medicalRecord;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = User::factory()->create(['role' => 'doctor']);
        $this->client = User::factory()->create(['role' => 'client']);
        $this->pet = Pet::factory()->create(['user_id' => $this->client->id]);

        $this->medicalRecord = MedicalRecord::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'visited_at' => now(),
            'reason' => 'Control clínico',
            'diagnosis' => 'Paciente estable',
            'treatment' => 'Seguimiento rutinario',
            'observation' => 'Observación base para la consulta',
        ]);
    }

    /**
     * Happy path: el doctor sube un examen, queda vinculado a la mascota y luego
     * puede visualizarlo y descargarlo.
     */
    public function test_doctor_can_upload_view_and_download_medical_exam(): void
    {
        Storage::fake('local');

        $this->actingAs($this->doctor);

        $file = UploadedFile::fake()->create('hemograma.pdf', 120, 'application/pdf');

        $response = $this->post(route('medical_exams.store', $this->pet), [
            'medical_record_id' => $this->medicalRecord->id,
            'title' => 'Hemograma externo',
            'description' => 'Examen de laboratorio externo',
            'category' => 'Laboratorio',
            'exam_date' => now()->toDateString(),
            'files' => [$file],
        ]);

        $response->assertRedirect(route('medical_records.show', $this->pet));
        $response->assertSessionHas('success', 'Se cargaron 1 archivo(s) de examen correctamente.');

        $exam = MedicalExam::query()->first();
        $this->assertNotNull($exam);

        $this->assertDatabaseHas('medical_exams', [
            'pet_id' => $this->pet->id,
            'medical_record_id' => $this->medicalRecord->id,
            'uploaded_by' => $this->doctor->id,
            'title' => 'Hemograma externo',
            'original_name' => 'hemograma.pdf',
        ]);

        $this->assertTrue(Storage::disk('local')->exists($exam->file_path));

        $viewResponse = $this->get(route('medical_exams.view', $exam->id));
        $viewResponse->assertOk();
        $viewResponse->assertHeader('content-type', 'application/pdf; charset=UTF-8');

        $downloadResponse = $this->get(route('medical_exams.download', $exam->id));
        $downloadResponse->assertOk();
        $downloadResponse->assertDownload('hemograma.pdf');
    }

    /**
     * Caso negativo: un cliente no puede cargar un examen para una mascota ajena.
     */
    public function test_client_cannot_upload_exam_for_another_pet(): void
    {
        Storage::fake('local');

        $otherClient = User::factory()->create(['role' => 'client']);
        $otherPet = Pet::factory()->create(['user_id' => $otherClient->id]);

        $this->actingAs($this->client);

        $file = UploadedFile::fake()->create('radiografia.png', 120, 'image/png');

        $response = $this->post(route('medical_exams.store', $otherPet), [
            'medical_record_id' => null,
            'title' => 'Intento no autorizado',
            'description' => 'No debe almacenarse',
            'category' => 'Imagen',
            'exam_date' => now()->toDateString(),
            'files' => [$file],
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('medical_exams', [
            'pet_id' => $otherPet->id,
            'title' => 'Intento no autorizado',
        ]);
    }
}
