<?php

namespace Tests\Feature;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
            'observation' => 'Paciente en excelente estado', // CAMPO CLAVE PARA EL REQUEST
        ];

        // Ejecutamos la petición
        $response = $this->post(route('medical_records.store', $this->pet), $visitData);

        // Si sigue fallando, esto te imprimirá el error real en la consola de GitHub
        if ($response->status() !== 302) {
            dump($response->getSession()->get('errors')->getMessages());
        }

        $response->assertRedirect(route('medical_records.show', $this->pet));

        // Verificamos que se guardó como 'notes' (como hace tu controlador)
        $this->assertDatabaseHas('medical_records', [
            'pet_id' => $this->pet->id,
            'diagnosis' => 'Sano',
            'notes' => 'Paciente en excelente estado',
        ]);
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

    /**
     * Happy Path: Doctor can successfully update pet details and husbandry info.
     */
    public function test_doctor_can_update_pet_husbandry_and_details_successfully(): void
    {
        $this->actingAs($this->doctor);

        $updateData = [
            'name' => 'Noodle updated',
            'species' => 'Hurón Doméstico',
            'breed' => 'Sable Angora',
            'age' => 3,
            'gender' => 'male',
            'weight' => 1.50,
            'color' => 'Marrón oscuro',
            'size' => 'Pequeña',
            'reproductive_status' => 'Castrado',
            'is_deceased' => '0',
            'emotional_support' => '1',
            'service_animal' => '0',
            'diet' => 'Alimento premium especial',
            'diet_quantity' => '60g',
            'diet_frequency' => '3 veces al día',
            'housing' => 'Jaula grande con hamaca',
            'bath_frequency' => 'Cada 3 meses',
            'bath_products' => 'Champú avena',
            'other_pets' => 'Ninguna',
            'last_heat' => 'N/A',
        ];

        $response = $this->put(route('medical_records.update_pet', $this->pet), $updateData);

        $response->assertRedirect(route('medical_records.show', $this->pet));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pets', [
            'id' => $this->pet->id,
            'name' => 'Noodle updated',
            'species' => 'Hurón Doméstico',
            'breed' => 'Sable Angora',
            'age' => 3,
            'gender' => 'male',
            'weight' => 1.50,
            'color' => 'Marrón oscuro',
            'size' => 'Pequeña',
            'reproductive_status' => 'Castrado',
            'is_deceased' => false,
            'emotional_support' => true,
            'service_animal' => false,
            'diet' => 'Alimento premium especial',
            'diet_quantity' => '60g',
            'diet_frequency' => '3 veces al día',
            'housing' => 'Jaula grande con hamaca',
            'bath_frequency' => 'Cada 3 meses',
            'bath_products' => 'Champú avena',
            'other_pets' => 'Ninguna',
            'last_heat' => 'N/A',
        ]);
    }

    /**
     * Happy Path: Doctor can upload and update a pet photo.
     */
    public function test_doctor_can_update_pet_photo_successfully(): void
    {
        Storage::fake('public');
        $this->actingAs($this->doctor);

        $this->pet->setPhoto('pets/old_photo.jpg');
        $this->pet->save();

        $photo = UploadedFile::fake()->image('new_avatar.png');

        $updateData = [
            'name' => $this->pet->getName(),
            'species' => $this->pet->getSpecies(),
            'breed' => $this->pet->getBreed(),
            'age' => $this->pet->getAge(),
            'gender' => $this->pet->getGender(),
            'weight' => $this->pet->getWeight(),
            'color' => 'Gris',
            'is_deceased' => '0',
            'emotional_support' => '0',
            'service_animal' => '0',
            'photo' => $photo,
        ];

        $response = $this->put(route('medical_records.update_pet', $this->pet), $updateData);

        $response->assertRedirect(route('medical_records.show', $this->pet));

        $this->pet->refresh();
        $newPhotoPath = $this->pet->getPhoto();
        $this->assertNotNull($newPhotoPath);

        Storage::disk('public')->assertExists($newPhotoPath);
    }

    /**
     * Alternative Path: Clients cannot update pet details.
     */
    public function test_client_cannot_update_pet_details(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $this->actingAs($client);

        $response = $this->put(route('medical_records.update_pet', $this->pet), [
            'name' => 'Client Hacked Name',
            'species' => 'Rata',
            'gender' => 'female',
            'is_deceased' => '0',
            'emotional_support' => '0',
            'service_animal' => '0',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Alternative Path: Validation failure when updating pet details.
     */
    public function test_cannot_update_pet_with_invalid_data(): void
    {
        $this->actingAs($this->doctor);

        $response = $this->put(route('medical_records.update_pet', $this->pet), [
            'name' => '', // blank name
            'species' => 'Loro',
            'gender' => 'invalid_gender_value', // invalid gender option
            'is_deceased' => '1',
            'emotional_support' => '0',
            'service_animal' => '0',
        ]);

        $response->assertSessionHasErrors(['name', 'gender']);
    }

    /**
     * Test that the doctor can view client list with inline per-pet "Ver detalles" links,
     * chevron links, and "Editar Cliente" button.
     */
    public function test_doctor_can_view_client_list_with_per_pet_details_and_renamed_edit_button(): void
    {
        $this->actingAs($this->doctor);

        // Make sure there is a client with a pet
        $client = User::factory()->create(['role' => 'client', 'name' => 'John', 'lastname' => 'Doe']);
        $pet = Pet::factory()->create(['user_id' => $client->getId(), 'name' => 'Flippy', 'species' => 'Iguana Verde']);

        $response = $this->get('/doctor/clients');

        $response->assertStatus(200);
        $response->assertSee('Editar Cliente');
        $response->assertSee('Ver detalles');
        $response->assertSee('Flippy');
        $response->assertSee('Iguana Verde');
        $response->assertSee(route('medical_records.show', $pet->getId()));
    }

    /**
     * Test that the medical records view contains the appointments history,
     * count badges for all four categories, and doesn't contain deleted tabs.
     */
    public function test_medical_records_view_contains_appointments_and_all_badges(): void
    {
        $this->actingAs($this->doctor);

        // Create appointments for this pet
        $appointment = \App\Models\Appointment::create([
            'doctor_id' => $this->doctor->getId(),
            'pet_id' => $this->pet->getId(),
            'date' => '2026-06-01',
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'status' => 'scheduled',
            'reason' => 'Chequeo de caparazón',
        ]);

        // Create a vaccination
        \App\Models\Vaccination::create([
            'doctor_id' => $this->doctor->getId(),
            'pet_id' => $this->pet->getId(),
            'vaccine_type' => 'Vacuna Reptil A',
            'vaccinated_at' => now(),
            'next_due_date' => now()->addYear(),
        ]);

        // Create a medical exam
        \App\Models\MedicalExam::create([
            'uploaded_by' => $this->doctor->getId(),
            'pet_id' => $this->pet->getId(),
            'title' => 'Radiografía de cola',
            'original_name' => 'rayos_x.png',
            'file_path' => 'exams/rayos_x.png',
            'category' => 'Radiología',
            'mime_type' => 'image/png',
            'file_size' => 1234,
            'uploaded_at' => now(),
        ]);

        // Create a medical record
        MedicalRecord::create([
            'pet_id' => $this->pet->getId(),
            'doctor_id' => $this->doctor->getId(),
            'visited_at' => now(),
            'reason' => 'Control de peso',
            'diagnosis' => 'Normal',
            'notes' => 'Saludable',
        ]);

        $response = $this->get(route('medical_records.show', $this->pet));

        $response->assertStatus(200);

        // Verify counts are loaded in view variables
        $response->assertViewHas('appointments');
        $response->assertViewHas('vaccinations');
        $response->assertViewHas('medicalExams');
        $response->assertViewHas('medicalRecords');

        // Verify count badge elements exist in html
        $response->assertSee('Consultas');
        $response->assertSee('Vacunaciones');
        $response->assertSee('Exámenes de laboratorio');
        $response->assertSee('Citas');

        // Verify appointments section details are displayed
        $response->assertSee('Historial de Citas');
        $response->assertSee('Chequeo de caparazón');
        $response->assertSee('Programada');

        // Verify removed tabs are NOT present
        $response->assertDontSee('Desparasitaciones');
        $response->assertDontSee('Hospitalizaciones/a...');
        $response->assertDontSee('Cirugías/procedimie...');
        $response->assertDontSee('Guardería');
        $response->assertDontSee('Seguimientos');
        $response->assertDontSee('Remisiones');
    }

    public function test_doctor_can_create_medical_record_with_multiple_photos(): void
    {
        Storage::fake('public');
        $this->actingAs($this->doctor);

        $photo1 = UploadedFile::fake()->image('wound1.jpg');
        $photo2 = UploadedFile::fake()->image('wound2.jpg');

        $visitData = [
            'visited_at' => now()->toDateString(),
            'reason' => 'Herida',
            'diagnosis' => 'Herida leve',
            'treatment' => 'Limpieza',
            'observation' => 'Limpieza',
            'photos' => [$photo1, $photo2],
        ];

        $response = $this->post(route('medical_records.store', $this->pet), $visitData);
        $response->assertRedirect();

        $record = MedicalRecord::latest()->first();
        $this->assertNotNull($record->photos);
        $this->assertCount(2, $record->photos);
        
        Storage::disk('public')->assertExists($record->photos[0]);
        Storage::disk('public')->assertExists($record->photos[1]);
    }

    public function test_doctor_can_update_medical_record_with_photos(): void
    {
        Storage::fake('public');
        $this->actingAs($this->doctor);

        $record = MedicalRecord::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'visited_at' => now(),
            'reason' => 'Antigua',
            'diagnosis' => 'Antiguo',
            'notes' => 'Old',
            'photos' => ['medical_records/old1.jpg'] // Simulando una foto vieja
        ]);

        $newPhoto = UploadedFile::fake()->image('new.jpg');

        $response = $this->put(route('medical_records.update', $record), [
            'visited_at' => now()->toDateString(),
            'reason' => 'Nueva',
            'diagnosis' => 'Nuevo',
            'treatment' => 'Nuevo tratamiento',
            'observation' => 'New',
            'photos' => [$newPhoto],
        ]);

        $response->assertRedirect();
        
        $record->refresh();
        $this->assertEquals('Nueva', $record->reason);
        $this->assertCount(2, $record->photos); // Conserva la antigua + 1 nueva
    }

    public function test_doctor_can_delete_medical_record_with_photos(): void
    {
        Storage::fake('public');
        $this->actingAs($this->doctor);

        Storage::disk('public')->put('medical_records/dummy.jpg', 'content');

        $record = MedicalRecord::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'visited_at' => now(),
            'reason' => 'Borrar',
            'diagnosis' => 'Borrar',
            'notes' => 'Borrar',
            'photos' => ['medical_records/dummy.jpg']
        ]);

        $response = $this->delete(route('medical_records.destroy', $record));
        $response->assertRedirect();

        $this->assertDatabaseMissing('medical_records', ['id' => $record->id]);
        Storage::disk('public')->assertMissing('medical_records/dummy.jpg');
    }
}
