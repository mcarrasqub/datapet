<?php

namespace Tests\Feature;

use App\Models\MedicalExam;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * AC1: El Administrador tiene acceso total.
     */
    public function test_admin_can_access_administrative_modules(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'lastname' => 'Admin',
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('users.index'));

        $response->assertOk();
        $response->assertViewIs('admin.users.index');
    }

    /**
     * AC2: El Doctor accede a módulos operativos.
     */
    public function test_doctor_can_access_operational_modules(): void
    {
        $doctor = User::factory()->create([
            'role' => 'doctor',
            'lastname' => 'Doctor',
        ]);

        $this->actingAs($doctor);

        $response = $this->get(route('dashboard.index'));

        $response->assertOk();
    }

    /**
     * AC4: Un cliente no puede acceder a tareas administrativas.
     */
    public function test_client_cannot_access_admin_tasks_dashboard(): void
    {
        $client = User::factory()->create([
            'role' => 'client',
            'lastname' => 'Client',
        ]);

        $this->actingAs($client);

        $response = $this->get(route('tasks.index'));

        $response->assertStatus(403);
    }

    /**
     * AC4: Un no-admin no puede borrar usuarios.
     */
    public function test_non_admin_cannot_delete_users(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor', 'lastname' => 'Doc']);
        $otherUser = User::factory()->create(['role' => 'client', 'lastname' => 'User']);

        $this->actingAs($doctor);

        $response = $this->delete(route('users.destroy', $otherUser));

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $otherUser->id]);
    }

    /**
     * AC3: El cliente puede acceder a la vista de sus exámenes médicos.
     */
    public function test_client_access_medical_exam(): void
    {
        // 1. Simular el disco 'local' porque el controlador usa Storage::disk('local')
        Storage::fake('local');
        $filePath = 'exams/documento.pdf';
        Storage::disk('local')->put($filePath, 'contenido falso');

        $client = User::factory()->create(['role' => 'client', 'lastname' => 'Owner']);
        $pet = Pet::factory()->create(['user_id' => $client->id]);

        // 2. Crear el registro con todos los campos obligatorios
        $exam = MedicalExam::create([
            'pet_id' => $pet->id,
            'title' => 'Examen de Prueba',
            'original_name' => 'documento.pdf',
            'file_path' => $filePath,
            'mime_type' => 'application/pdf',
            'file_size' => 2048,
            'uploaded_by' => $client->id,
            'uploaded_at' => Carbon::now(),
        ]);

        $this->actingAs($client);

        // 3. Ejecutar la petición
        // Si el controlador usa Route Model Binding, pasar el objeto $exam es lo más seguro
        $response = $this->get(route('medical_exams.view', $exam));

        // El controlador puede devolver una BinaryFileResponse (archivo). Manejar ambos casos.
        if ($response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
            $this->assertEquals(200, $response->getStatusCode());
        } else {
            $response->assertStatus(200);
        }
    }
}
