<?php

namespace Tests\Feature;

use App\Models\AdoptionRequest;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdoptionAndClientUpdatesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin can view adoptions index (requests & pets) and the edit page.
     */
    public function test_admin_can_view_adoptions_index_and_edit_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $pet = Pet::factory()->create([
            'user_id' => $admin->getId(),
            'available_for_adoption' => true,
            'adoption_description' => 'Un loro muy parlanchín buscando hogar',
        ]);

        $client = User::factory()->create(['role' => 'client']);

        $request = AdoptionRequest::create([
            'pet_id' => $pet->getId(),
            'user_id' => $client->getId(),
            'full_name' => 'Carlos Cliente',
            'phone' => '1234567890',
            'status' => 'pending',
        ]);

        // Access index
        $response = $this->get('/admin/adoption-requests');
        $response->assertStatus(200);
        $response->assertSee($pet->getName());
        $response->assertSee($client->name);
        $response->assertSee('Un loro muy parlanchín buscando hogar');

        // Access edit page
        $response = $this->get(route('admin.adoptions.edit', $pet->getId()));
        $response->assertStatus(200);
        $response->assertSee($pet->getName());
        $response->assertSee('Editar Mascota en Adopción');
    }

    /**
     * Test admin can update pet adoption details successfully.
     */
    public function test_admin_can_update_pet_adoption_details_successfully(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $pet = Pet::factory()->create([
            'user_id' => $admin->getId(),
            'available_for_adoption' => true,
            'adoption_description' => 'Un loro genial',
        ]);

        Storage::fake('public');
        $fakePhoto = UploadedFile::fake()->image('test_parrot.jpg');

        $data = [
            'name' => 'Kiko El Loro',
            'species' => 'Loro Guacamayo',
            'age' => 4,
            'weight' => 1.8,
            'available_for_adoption' => '1',
            'adoption_description' => 'Loro muy cariñoso y de colores brillantes.',
            'photo' => $fakePhoto,
        ];

        $response = $this->put(route('admin.adoptions.update', $pet->getId()), $data);

        $response->assertRedirect('/admin/adoption-requests');
        $response->assertSessionHas('success', 'Mascota de adopción actualizada correctamente');

        $this->assertDatabaseHas('pets', [
            'id' => $pet->getId(),
            'name' => 'Kiko El Loro',
            'species' => 'Loro Guacamayo',
            'age' => 4,
            'weight' => 1.8,
            'available_for_adoption' => true,
            'adoption_description' => 'Loro muy cariñoso y de colores brillantes.',
        ]);

        $pet->refresh();
        $this->assertNotNull($pet->getPhoto());
        Storage::disk('public')->assertExists($pet->getPhoto());
    }

    /**
     * Test non-admin cannot access adoption admin routes.
     */
    public function test_non_admin_cannot_access_adoption_admin_routes(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $this->actingAs($doctor);

        $pet = Pet::factory()->create([
            'user_id' => $doctor->getId(),
            'available_for_adoption' => true,
        ]);

        // Get index
        $response = $this->get('/admin/adoption-requests');
        $response->assertStatus(403);

        // Get edit page
        $response = $this->get(route('admin.adoptions.edit', $pet->getId()));
        $response->assertStatus(403);

        // Put update
        $response = $this->put(route('admin.adoptions.update', $pet->getId()), [
            'name' => 'Violación de Seguridad',
            'species' => 'Intruso',
        ]);
        $response->assertStatus(403);
    }

    public function test_admin_can_approve_adoption_request(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $client = User::factory()->create(['role' => 'client']);
        $pet = Pet::factory()->create(['user_id' => $admin->id, 'available_for_adoption' => true]);

        $request = AdoptionRequest::create([
            'pet_id' => $pet->id,
            'user_id' => $client->id,
            'full_name' => 'Carlos',
            'phone' => '123',
            'status' => 'pending',
        ]);

        $response = $this->patch(route('adoption.approve', $request));
        $response->assertRedirect();

        $request->refresh();
        $this->assertEquals('approved', $request->status);
    }

    public function test_admin_can_reject_adoption_request(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $client = User::factory()->create(['role' => 'client']);
        $pet = Pet::factory()->create(['user_id' => $admin->id, 'available_for_adoption' => true]);

        $request = AdoptionRequest::create([
            'pet_id' => $pet->id,
            'user_id' => $client->id,
            'full_name' => 'Carlos',
            'phone' => '123',
            'status' => 'pending',
        ]);

        $response = $this->patch(route('adoption.reject', $request));
        $response->assertRedirect();

        $request->refresh();
        $this->assertEquals('rejected', $request->status);
    }

    public function test_admin_can_create_pet_for_adoption_via_store_request(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        Storage::fake('public');

        $fakePhoto = UploadedFile::fake()->image('new_adopt.jpg');

        $data = [
            'name' => 'Firulais',
            'species' => 'Perro',
            'age' => 2,
            'weight' => 15,
            'available_for_adoption' => '1',
            'adoption_description' => 'Muy juguetón',
            'photo' => $fakePhoto,
        ];

        $response = $this->post(route('admin.adoptions.store'), $data);
        $response->assertRedirect(route('adoption.admin.index'));

        $this->assertDatabaseHas('pets', [
            'name' => 'Firulais',
            'available_for_adoption' => true,
        ]);
    }

    /**
     * Test doctor can update client contact details successfully.
     */
    public function test_doctor_can_update_client_personal_info(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $this->actingAs($doctor);

        $client = User::factory()->create([
            'role' => 'client',
            'name' => 'Carlos',
            'lastname' => 'Gómez',
            'email' => 'carlos.gomez@example.com',
            'phone' => '5551234',
        ]);

        $data = [
            'name' => 'Carlos Alberto',
            'lastname' => 'Gómez Restrepo',
            'email' => 'carlos.alberto@example.com',
            'phone' => '3009998877',
            'address' => 'Carrera 45 #80-90',
        ];

        $response = $this->put(route('doctor.clients.update', $client->getId()), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Datos del cliente actualizados correctamente.');

        $this->assertDatabaseHas('users', [
            'id' => $client->getId(),
            'name' => 'Carlos Alberto',
            'lastname' => 'Gómez Restrepo',
            'email' => 'carlos.alberto@example.com',
            'phone' => '3009998877',
            'address' => 'Carrera 45 #80-90',
        ]);
    }

    /**
     * Test non-doctor cannot update client personal details.
     */
    public function test_non_doctor_cannot_update_client_personal_info(): void
    {
        $clientA = User::factory()->create(['role' => 'client']);
        $clientB = User::factory()->create(['role' => 'client']);
        $this->actingAs($clientA);

        $response = $this->put(route('doctor.clients.update', $clientB->getId()), [
            'name' => 'Ataque',
            'lastname' => 'Malicioso',
            'email' => 'hacker@example.com',
            'phone' => '999999',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test client update validation rules.
     */
    public function test_client_update_validation_rules(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $this->actingAs($doctor);

        $client = User::factory()->create([
            'role' => 'client',
            'email' => 'original@example.com',
        ]);

        $otherClient = User::factory()->create([
            'role' => 'client',
            'email' => 'taken@example.com',
        ]);

        // Scenario A: Missing fields
        $response = $this->put(route('doctor.clients.update', $client->getId()), [
            'address' => 'Calle 100',
        ]);
        $response->assertSessionHasErrors(['name', 'lastname', 'email', 'phone']);

        // Scenario B: Duplicate email
        $response = $this->put(route('doctor.clients.update', $client->getId()), [
            'name' => 'Carlos',
            'lastname' => 'Gómez',
            'email' => 'taken@example.com',
            'phone' => '5551234',
        ]);
        $response->assertSessionHasErrors(['email']);
    }
}
