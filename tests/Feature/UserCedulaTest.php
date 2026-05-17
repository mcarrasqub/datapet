<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCedulaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful registration of a new client with a valid 10-digit Cédula and pet.
     */
    public function test_new_client_registration_with_valid_cedula_succeeds(): void
    {
        // Doctor or general authenticated session since registration requires login in this system
        $doctor = User::factory()->create(['role' => 'doctor']);
        $this->actingAs($doctor);

        $data = [
            'registration_type' => 'new_client',
            'id' => '1020304050', // Valid 10-digit Cédula
            'name' => 'Mariana',
            'lastname' => 'Carrasquilla',
            'email' => 'mariana.exotic@datapet.com',
            'phone' => '3001234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'address' => 'Calle 10 #20-30',
            'pet_name' => 'Noodle',
            'species' => 'Hurón',
            'gender' => 'male',
            'weight' => 1.25,
        ];

        $response = $this->post('/register', $data);

        $response->assertRedirect('/register');
        $response->assertSessionHas('success', 'Cliente y mascota registrados exitosamente.');

        // Verify user was created with correct Cédula (id)
        $this->assertDatabaseHas('users', [
            'id' => 1020304050,
            'name' => 'Mariana',
            'lastname' => 'Carrasquilla',
            'role' => 'client',
        ]);

        // Verify pet was linked to correct Cédula
        $this->assertDatabaseHas('pets', [
            'user_id' => 1020304050,
            'name' => 'Noodle',
            'species' => 'Hurón',
        ]);
    }

    /**
     * Test validation rules for new client registration with invalid Cédulas.
     */
    public function test_new_client_registration_validation_fails_for_invalid_cedulas(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $this->actingAs($doctor);

        // Scenario A: Missing Cédula
        $dataMissing = [
            'registration_type' => 'new_client',
            'name' => 'Mariana',
            'lastname' => 'Carrasquilla',
            'email' => 'mariana.exotic@datapet.com',
            'phone' => '3001234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'pet_name' => 'Noodle',
            'species' => 'Hurón',
            'gender' => 'male',
        ];
        $response = $this->post('/register', $dataMissing);
        $response->assertSessionHasErrors(['id']);

        // Scenario B: Non-numeric Cédula
        $dataAlpha = array_merge($dataMissing, ['id' => '102030abc0']);
        $response = $this->post('/register', $dataAlpha);
        $response->assertSessionHasErrors(['id']);

        // Scenario C: Less than 10 digits
        $dataShort = array_merge($dataMissing, ['id' => '123456789']);
        $response = $this->post('/register', $dataShort);
        $response->assertSessionHasErrors(['id']);

        // Scenario D: More than 10 digits
        $dataLong = array_merge($dataMissing, ['id' => '12345678901']);
        $response = $this->post('/register', $dataLong);
        $response->assertSessionHasErrors(['id']);

        // Scenario E: Duplicate Cédula
        User::factory()->create(['id' => 1020304050, 'email' => 'other@datapet.com']);
        $dataDuplicate = array_merge($dataMissing, ['id' => '1020304050']);
        $response = $this->post('/register', $dataDuplicate);
        $response->assertSessionHasErrors(['id']);
    }

    /**
     * Test successful creation of client via Admin Client Management with valid Cédula.
     */
    public function test_admin_can_create_client_with_valid_cedula(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $data = [
            'id' => '1000200030', // Valid 10-digit Cédula
            'name' => 'Juan',
            'lastname' => 'Pérez',
            'email' => 'juan.perez@datapet.com',
            'phone' => '3119876543',
            'address' => 'Avenida Principal 456',
            'password' => 'secure_password',
            'password_confirmation' => 'secure_password',
        ];

        $response = $this->post('/clients', $data);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success', 'Cliente creado exitosamente.');

        $this->assertDatabaseHas('users', [
            'id' => 1000200030,
            'name' => 'Juan',
            'lastname' => 'Pérez',
            'role' => 'client',
        ]);
    }

    /**
     * Test validation rules for client creation via panel with invalid Cédulas.
     */
    public function test_client_creation_validation_fails_for_invalid_cedulas(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Scenario A: Missing Cédula
        $data = [
            'name' => 'Juan',
            'lastname' => 'Pérez',
            'email' => 'juan.perez@datapet.com',
            'phone' => '3119876543',
            'password' => 'secure_password',
            'password_confirmation' => 'secure_password',
        ];
        $response = $this->post('/clients', $data);
        $response->assertSessionHasErrors(['id']);

        // Scenario B: Non-numeric
        $response = $this->post('/clients', array_merge($data, ['id' => 'abcde12345']));
        $response->assertSessionHasErrors(['id']);

        // Scenario C: Invalid length
        $response = $this->post('/clients', array_merge($data, ['id' => '10002000']));
        $response->assertSessionHasErrors(['id']);
    }

    /**
     * Test successful creation of a general user (doctor) via Admin User Management with valid Cédula.
     */
    public function test_admin_can_create_user_with_valid_cedula(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $data = [
            'id' => '9999888877', // Valid 10-digit Cédula
            'name' => 'Fernando Alzate',
            'email' => 'fernando.alzate@datapet.com',
            'role' => 'doctor',
            'password' => 'doctor_password123',
        ];

        $response = $this->post('/users', $data);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success', 'Usuario "Fernando" creado correctamente.');

        $this->assertDatabaseHas('users', [
            'id' => 9999888877,
            'name' => 'Fernando',
            'lastname' => 'Alzate',
            'role' => 'doctor',
        ]);
    }

    /**
     * Test validation rules for general user creation with invalid Cédulas.
     */
    public function test_user_creation_validation_fails_for_invalid_cedulas(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $data = [
            'name' => 'Dr. Fernando Alzate',
            'email' => 'fernando.alzate@datapet.com',
            'role' => 'doctor',
            'password' => 'doctor_password123',
        ];

        // Scenario A: Missing Cédula
        $response = $this->post('/users', $data);
        $response->assertSessionHasErrors(['id']);

        // Scenario B: Non-numeric
        $response = $this->post('/users', array_merge($data, ['id' => 'abcdefghij']));
        $response->assertSessionHasErrors(['id']);

        // Scenario C: Invalid length
        $response = $this->post('/users', array_merge($data, ['id' => '99998888']));
        $response->assertSessionHasErrors(['id']);
    }

    /**
     * Test doctor can search clients using the Cédula.
     */
    public function test_doctor_can_search_clients_by_cedula(): void
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $this->actingAs($doctor);

        // Create clients with different Cédulas
        $clientA = User::factory()->create([
            'id' => 1020304050,
            'name' => 'Mariana',
            'lastname' => 'Carrasquilla',
            'role' => 'client',
        ]);
        $clientB = User::factory()->create([
            'id' => 9988776655,
            'name' => 'Juan',
            'lastname' => 'Pérez',
            'role' => 'client',
        ]);

        // Search for Mariana's Cédula
        $response = $this->get('/doctor/clients?client_search=1020304050');
        $response->assertStatus(200);
        $response->assertSee('Mariana Carrasquilla');
        $response->assertDontSee('Juan Pérez');

        // Search for Mariana's Cédula prefix
        $response = $this->get('/doctor/clients?client_search=102030');
        $response->assertStatus(200);
        $response->assertSee('Mariana Carrasquilla');
        $response->assertDontSee('Juan Pérez');
    }
}
