<?php

namespace Tests\Feature;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use App\Models\MedicalExam;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalExamsTest extends TestCase
{
    use RefreshDatabase;

    private User $doctor;
    private Pet $pet;
    private MedicalRecord $medicalRecord;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Usamos el disco 'local' que es el definido en tu controlador
        Storage::fake('local');

        $this->doctor = User::factory()->create(['role' => 'doctor']);
        $client = User::factory()->create(['role' => 'client']);
        $this->pet = Pet::factory()->create(['user_id' => $client->id]);

        $this->medicalRecord = MedicalRecord::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'visited_at' => now(),
            'reason' => 'Control',
            'diagnosis' => 'Estable',
            'treatment' => 'N/A',
            'notes' => 'Mascota sana', 
        ]);
    }

    /**
     * Test de carga de archivos (Store)
     */
    public function test_doctor_can_upload_medical_exam(): void
    {
        $this->actingAs($this->doctor);
        
        $file = UploadedFile::fake()->create('resultado.pdf', 500);

        $data = [
            'medical_record_id' => $this->medicalRecord->id,
            'title' => 'Hemograma',
            'description' => 'Resultados normales',
            'category' => 'Laboratorio',
            'exam_date' => now()->toDateString(),
            'files' => [$file],
        ];

        $response = $this->post(route('medical_exams.store', $this->pet), $data);

        $response->assertRedirect();
        
        // Verificación de base de datos
        $this->assertDatabaseHas('medical_exams', [
            'title' => 'Hemograma',
            'pet_id' => $this->pet->id
        ]);

        // Verificación física del archivo
        $exam = MedicalExam::latest()->first();
        $this->assertTrue(
            Storage::disk('local')->exists($exam->file_path), 
            "El archivo no existe en: " . $exam->file_path
        );
    }

    /**
     * Test de visualización (View) - Corregido para evitar error de Header
     */
    public function test_doctor_can_view_medical_exam(): void
    {
        $this->actingAs($this->doctor);
        
        // 1. Crear el archivo físicamente en el disco fake
        $fileName = 'test_view.pdf';
        $relativePath = 'medical_exams/pet_' . $this->pet->id;
        $fullPath = $relativePath . '/' . $fileName;
        
        Storage::disk('local')->put($fullPath, 'Fake PDF Content');

        // 2. Crear el registro en la BD apuntando a ese archivo
        $exam = MedicalExam::create([
            'pet_id' => $this->pet->id,
            'medical_record_id' => $this->medicalRecord->id,
            'uploaded_by' => $this->doctor->id,
            'title' => 'Radiografia',
            'file_path' => $fullPath,
            'original_name' => $fileName,
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_at' => now(),
        ]);

        $response = $this->get(route('medical_exams.view', $exam));
        
        $response->assertOk();
        
        // Usamos una expresión regular o aserción parcial para ignorar el charset
        $this->assertStringContainsString(
            'application/pdf', 
            $response->headers->get('Content-Type')
        );
    }

    public function test_doctor_can_complete_medical_exam_review(): void
    {
        $this->actingAs($this->doctor);

        $exam = MedicalExam::create([
            'pet_id' => $this->pet->id,
            'medical_record_id' => $this->medicalRecord->id,
            'uploaded_by' => $this->doctor->id,
            'title' => 'Radiografia',
            'file_path' => 'dummy',
            'original_name' => 'dummy.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_at' => now(),
        ]);

        $this->assertNull($exam->reviewed_by_doctor_at);

        $response = $this->post(route('medical_exams.complete_review', $exam));

        $response->assertRedirect();
        
        $exam->refresh();
        $this->assertNotNull($exam->reviewed_by_doctor_at);
        $this->assertEquals($this->doctor->id, $exam->reviewed_by_doctor_id);
    }

    /**
     * Test de descarga (Download) - Para subir cobertura
     */
    public function test_doctor_can_download_medical_exam(): void
    {
        $this->actingAs($this->doctor);
        
        $fileName = 'descarga.pdf';
        $fullPath = 'medical_exams/pet_' . $this->pet->id . '/' . $fileName;
        Storage::disk('local')->put($fullPath, 'Contenido');

        $exam = MedicalExam::create([
            'pet_id' => $this->pet->id,
            'uploaded_by' => $this->doctor->id,
            'title' => 'Descarga',
            'file_path' => $fullPath,
            'original_name' => $fileName,
            'mime_type' => 'application/pdf',
            'file_size' => 500,
            'uploaded_at' => now(),
        ]);

        $response = $this->get(route('medical_exams.download', $exam));
        
        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename=descarga.pdf');
    }

    public function test_client_cannot_upload_to_other_pets(): void
    {
        $otherClient = User::factory()->create(['role' => 'client']);
        $otherPet = Pet::factory()->create(['user_id' => $otherClient->id]);
        
        $maliciousClient = User::factory()->create(['role' => 'client']);
        $this->actingAs($maliciousClient);

        $file = UploadedFile::fake()->create('hack.pdf', 100);

        $response = $this->post(route('medical_exams.store', $otherPet), [
            'files' => [$file],
        ]);

        $response->assertStatus(403);
    }
}