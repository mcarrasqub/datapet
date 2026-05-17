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
            'notes' => 'Paciente en excelente estado' 
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
}