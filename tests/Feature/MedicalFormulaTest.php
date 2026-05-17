<?php

namespace Tests\Feature;

use App\Models\MedicalFormula;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalFormulaTest extends TestCase
{
    use RefreshDatabase;

    private User $doctor;

    private User $client;

    private Pet $pet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = User::factory()->create(['role' => 'doctor']);
        $this->client = User::factory()->create(['role' => 'client']);

        $this->pet = Pet::create([
            'user_id' => $this->client->id,
            'name' => 'Kiko',
            'species' => 'Conejo',
            'breed' => 'Mini Lop',
            'age' => 2,
            'gender' => 'male',
            'weight' => 1.8,
            'available_for_adoption' => false,
        ]);
    }

    public function test_doctor_can_store_medical_formula_happy_path()
    {
        $this->actingAs($this->doctor);

        $formulaData = [
            'formula_date' => '2026-05-17',
            'instructions' => 'Dar con abundante agua y heno fresco.',
            'medications' => [
                [
                    'name' => 'Meloxicam',
                    'dose' => '0.2 ml',
                    'frequency' => 'Cada 24 horas',
                    'duration' => '5 días',
                ],
                [
                    'name' => 'Enrofloxacina',
                    'dose' => '0.5 ml',
                    'frequency' => 'Cada 12 horas',
                    'duration' => '7 días',
                ],
            ],
        ];

        $response = $this->post(route('formulas.store', $this->pet), $formulaData);

        $response->assertRedirect(route('medical_records.show', $this->pet));
        $response->assertSessionHas('success', 'Fórmula médica guardada exitosamente.');

        $this->assertDatabaseHas('medical_formulas', [
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'instructions' => 'Dar con abundante agua y heno fresco.',
        ]);

        $formula = MedicalFormula::first();
        $this->assertEquals('2026-05-17', $formula->formula_date->format('Y-m-d'));
        $this->assertCount(2, $formula->medications);
        $this->assertEquals('Meloxicam', $formula->medications[0]['name']);
        $this->assertEquals('Enrofloxacina', $formula->medications[1]['name']);
    }

    public function test_formulas_appear_in_pet_profile()
    {
        $this->actingAs($this->doctor);

        $formula = MedicalFormula::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'formula_date' => '2026-05-17',
            'instructions' => 'Dar con abundante agua y heno fresco.',
            'medications' => [
                [
                    'name' => 'Meloxicam',
                    'dose' => '0.2 ml',
                    'frequency' => 'Cada 24 horas',
                    'duration' => '5 días',
                ],
            ],
        ]);

        $response = $this->get(route('medical_records.show', $this->pet));

        $response->assertStatus(200);
        $response->assertSee('Fórmulas Médicas');
        $response->assertSee('2026-05-17');
        $response->assertSee('Meloxicam');
        $response->assertSee('0.2 ml');
        $response->assertSee('Cada 24 horas');
        $response->assertSee('5 días');
        $response->assertSee('Dar con abundante agua y heno fresco.');
        $response->assertSee('Dr(a). '.$this->doctor->name);
    }

    public function test_validation_fails_when_medications_missing()
    {
        $this->actingAs($this->doctor);

        $formulaData = [
            'formula_date' => '2026-05-17',
            'instructions' => 'Recomendación general',
            'medications' => [],
        ];

        $response = $this->post(route('formulas.store', $this->pet), $formulaData);

        $response->assertSessionHasErrors(['medications']);
    }

    public function test_validation_fails_when_required_medication_fields_missing()
    {
        $this->actingAs($this->doctor);

        $formulaData = [
            'formula_date' => '2026-05-17',
            'instructions' => 'Recomendación general',
            'medications' => [
                [
                    'name' => '', // Empty name
                    'dose' => '0.2 ml',
                    'frequency' => '', // Empty frequency
                    'duration' => '5 días',
                ],
            ],
        ];

        $response = $this->post(route('formulas.store', $this->pet), $formulaData);

        $response->assertSessionHasErrors([
            'medications.0.name',
            'medications.0.frequency',
        ]);
    }

    public function test_unauthorized_users_cannot_access_formula_routes()
    {
        // 1. Client role cannot store formulas
        $this->actingAs($this->client);

        $formulaData = [
            'formula_date' => '2026-05-17',
            'medications' => [
                [
                    'name' => 'Meloxicam',
                    'dose' => '0.2 ml',
                    'frequency' => 'Cada 24 horas',
                    'duration' => '5 días',
                ],
            ],
        ];

        $responseStore = $this->post(route('formulas.store', $this->pet), $formulaData);
        $responseStore->assertStatus(403);

        // 2. Doctor creates a formula, client attempts to delete it
        $formula = MedicalFormula::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'formula_date' => '2026-05-17',
            'medications' => [
                [
                    'name' => 'Meloxicam',
                    'dose' => '0.2 ml',
                    'frequency' => 'Cada 24 horas',
                    'duration' => '5 días',
                ],
            ],
        ]);

        $responseDelete = $this->delete(route('formulas.destroy', $formula));
        $responseDelete->assertStatus(403);

        // 3. Guest is redirected
        $this->post(route('logout'));

        $responseGuest = $this->post(route('formulas.store', $this->pet), $formulaData);
        $responseGuest->assertRedirect('/login');
    }

    public function test_doctor_can_delete_medical_formula()
    {
        $this->actingAs($this->doctor);

        $formula = MedicalFormula::create([
            'pet_id' => $this->pet->id,
            'doctor_id' => $this->doctor->id,
            'formula_date' => '2026-05-17',
            'medications' => [
                [
                    'name' => 'Meloxicam',
                    'dose' => '0.2 ml',
                    'frequency' => 'Cada 24 horas',
                    'duration' => '5 días',
                ],
            ],
        ]);

        $this->assertDatabaseHas('medical_formulas', ['id' => $formula->id]);

        $response = $this->delete(route('formulas.destroy', $formula));

        $response->assertRedirect(route('medical_records.show', $this->pet));
        $response->assertSessionHas('success', 'Fórmula médica eliminada exitosamente.');

        $this->assertDatabaseMissing('medical_formulas', ['id' => $formula->id]);
    }
}
