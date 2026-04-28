<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\User;
use App\Models\Vaccination;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class VaccinationTest extends TestCase
{
    public function test_doctor_can_store_vaccination_happy_path()
    {
        $doctor = User::factory()->create(['role' => 'doctor', 'lastname' => 'Doctor']);
        $owner = User::factory()->create(['role' => 'client', 'lastname' => 'Owner']);
        $pet = Pet::create([
            'user_id' => $owner->id,
            'name' => 'Luna',
            'species' => 'Perro',
            'gender' => 'female',
        ]);

        $payload = [
            'vaccine_type' => 'Antirrabica',
            'vaccinated_at' => Carbon::today()->toDateString(),
            'next_due_date' => Carbon::today()->addYear()->toDateString(),
            'notes' => 'Primera dosis',
        ];

        $response = $this->actingAs($doctor)
            ->post(route('vaccinations.store', $pet), $payload);

        $response->assertRedirect(route('medical_records.show', $pet));
        $this->assertDatabaseHas('vaccinations', [
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'vaccine_type' => 'Antirrabica',
        ]);

        $vaccination = Vaccination::first();
        $this->assertNotNull($vaccination);
        $this->assertSame('Antirrabica', $vaccination->vaccine_type);
        $this->assertSame(Carbon::today()->toDateString(), $vaccination->vaccinated_at->toDateString());
        $this->assertSame(Carbon::today()->addYear()->toDateString(), $vaccination->next_due_date->toDateString());
    }

    public function test_vaccinations_appear_in_pet_profile()
    {
        $doctor = User::factory()->create(['role' => 'doctor', 'lastname' => 'Doctor']);
        $owner = User::factory()->create(['role' => 'client', 'lastname' => 'Owner']);
        $pet = Pet::create([
            'user_id' => $owner->id,
            'name' => 'Luna',
            'species' => 'Perro',
            'gender' => 'female',
        ]);

        Vaccination::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'vaccine_type' => 'Antirrabica',
            'vaccinated_at' => Carbon::today(),
            'next_due_date' => Carbon::today()->addYear(),
            'notes' => 'Refuerzo anual',
        ]);

        $response = $this->actingAs($doctor)->get(route('medical_records.show', $pet));

        $response->assertOk();
        $response->assertSee('Antirrabica');
        $response->assertSee('Refuerzo anual');
        $response->assertSee(Carbon::today()->addYear()->format('Y-m-d'));
    }

    public function test_validation_fails_when_required_fields_missing()
    {
        $doctor = User::factory()->create(['role' => 'doctor', 'lastname' => 'Doctor']);
        $owner = User::factory()->create(['role' => 'client', 'lastname' => 'Owner']);
        $pet = Pet::create([
            'user_id' => $owner->id,
            'name' => 'Luna',
            'species' => 'Perro',
            'gender' => 'female',
        ]);

        $payload = [
            // missing vaccine_type and vaccinated_at
            'next_due_date' => Carbon::today()->addYear()->toDateString(),
        ];

        $response = $this->actingAs($doctor)->post(route('vaccinations.store', $pet), $payload);

        $response->assertSessionHasErrors(['vaccine_type', 'vaccinated_at']);
        $this->assertDatabaseMissing('vaccinations', ['pet_id' => $pet->id]);
    }

    public function test_validation_fails_when_vaccinated_at_in_future()
    {
        $doctor = User::factory()->create(['role' => 'doctor', 'lastname' => 'Doctor']);
        $owner = User::factory()->create(['role' => 'client', 'lastname' => 'Owner']);
        $pet = Pet::create([
            'user_id' => $owner->id,
            'name' => 'Luna',
            'species' => 'Perro',
            'gender' => 'female',
        ]);

        $payload = [
            'vaccine_type' => 'Test',
            'vaccinated_at' => Carbon::tomorrow()->toDateString(),
        ];

        $response = $this->actingAs($doctor)->post(route('vaccinations.store', $pet), $payload);

        $response->assertSessionHasErrors(['vaccinated_at']);
        $this->assertDatabaseMissing('vaccinations', ['pet_id' => $pet->id]);
    }

    public function test_validation_fails_when_next_due_before_vaccinated_at()
    {
        $doctor = User::factory()->create(['role' => 'doctor', 'lastname' => 'Doctor']);
        $owner = User::factory()->create(['role' => 'client', 'lastname' => 'Owner']);
        $pet = Pet::create([
            'user_id' => $owner->id,
            'name' => 'Luna',
            'species' => 'Perro',
            'gender' => 'female',
        ]);

        $payload = [
            'vaccine_type' => 'Combo',
            'vaccinated_at' => Carbon::today()->toDateString(),
            'next_due_date' => Carbon::yesterday()->toDateString(),
        ];

        $response = $this->actingAs($doctor)->post(route('vaccinations.store', $pet), $payload);

        $response->assertSessionHasErrors(['next_due_date']);
        $this->assertDatabaseMissing('vaccinations', ['pet_id' => $pet->id]);
    }

    public function test_guest_is_redirected_to_login()
    {
        $owner = User::factory()->create(['role' => 'client', 'lastname' => 'Owner']);
        $pet = Pet::create([
            'user_id' => $owner->id,
            'name' => 'Luna',
            'species' => 'Perro',
            'gender' => 'female',
        ]);

        $payload = [
            'vaccine_type' => 'Antirrabica',
            'vaccinated_at' => Carbon::today()->toDateString(),
        ];

        $response = $this->post(route('vaccinations.store', $pet), $payload);

        $response->assertRedirect(route('login'));
    }
}
