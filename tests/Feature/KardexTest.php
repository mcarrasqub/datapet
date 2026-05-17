<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\User;
use App\Models\KardexEntry;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class KardexTest extends TestCase
{
    public function test_doctor_can_store_kardex_entries_for_different_species()
    {
        $doctor = User::factory()->create(['role' => 'doctor', 'lastname' => 'Doctor']);
        $owner = User::factory()->create(['role' => 'client', 'lastname' => 'Owner']);
        $pet = Pet::create([
            'user_id' => $owner->id,
            'name' => 'Kiko',
            'species' => 'Conejo',
            'gender' => 'male',
        ]);

        // 1. Huron Kardex
        $huronPayload = [
            'entry_date' => Carbon::today()->toDateString(),
            'animal_type' => 'huron',
            'parameters' => [
                'frecuencia_cardiaca' => 220,
                'frecuencia_respiratoria' => 35,
                'temperatura' => 38.6,
                'glicemia' => 85,
                'hidratacion' => 100,
            ]
        ];

        $response = $this->actingAs($doctor)->post(route('kardex.store', $pet), $huronPayload);
        $response->assertRedirect(route('medical_records.show', $pet));

        $this->assertDatabaseHas('kardex_entries', [
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'animal_type' => 'huron',
        ]);

        // 2. Loro Kardex
        $loroPayload = [
            'entry_date' => Carbon::today()->toDateString(),
            'animal_type' => 'loro',
            'parameters' => [
                'frecuencia_respiratoria' => 28,
                'temperatura_cloacal' => 41.2,
                'plumaje' => 'Excelente',
                'consistencia_heces' => 'Normal',
                'comportamiento' => 'Activo/Alerta',
                'estado_buche' => 'Lleno/Normal',
            ]
        ];

        $response = $this->actingAs($doctor)->post(route('kardex.store', $pet), $loroPayload);
        $response->assertRedirect(route('medical_records.show', $pet));

        $this->assertDatabaseHas('kardex_entries', [
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'animal_type' => 'loro',
        ]);

        // 3. Conejo Kardex
        $conejoPayload = [
            'entry_date' => Carbon::today()->toDateString(),
            'animal_type' => 'conejo',
            'parameters' => [
                'frecuencia_cardiaca' => 200,
                'frecuencia_respiratoria' => 40,
                'temperatura' => 39.1,
                'motilidad_intestinal' => 'Normal',
                'cecotrofos' => 'Normal/Heces Firmes',
                'estado_dental' => 'Perfecto estado',
            ]
        ];

        $response = $this->actingAs($doctor)->post(route('kardex.store', $pet), $conejoPayload);
        $response->assertRedirect(route('medical_records.show', $pet));

        $this->assertDatabaseHas('kardex_entries', [
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'animal_type' => 'conejo',
        ]);

        // 4. Erizo Kardex
        $erizoPayload = [
            'entry_date' => Carbon::today()->toDateString(),
            'animal_type' => 'erizo',
            'parameters' => [
                'frecuencia_cardiaca' => 240,
                'frecuencia_respiratoria' => 32,
                'temperatura' => 36.2,
                'estado_piel_puas' => 'Sin descamación',
                'enrollamiento' => 'Completo/Firme',
                'peso' => 420,
            ]
        ];

        $response = $this->actingAs($doctor)->post(route('kardex.store', $pet), $erizoPayload);
        $response->assertRedirect(route('medical_records.show', $pet));

        $this->assertDatabaseHas('kardex_entries', [
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'animal_type' => 'erizo',
        ]);

        // 5. Iguana Kardex
        $iguanaPayload = [
            'entry_date' => Carbon::today()->toDateString(),
            'animal_type' => 'iguana',
            'parameters' => [
                'frecuencia_cardiaca' => 45,
                'temperatura_terrario' => 31.5,
                'muda_piel' => 'Completa/Saludable',
                'hidratacion' => 'Normal/Turgente',
                'cola_extremidades' => 'Sanas',
                'coloracion' => 'Brillante/Verde Intenso',
            ]
        ];

        $response = $this->actingAs($doctor)->post(route('kardex.store', $pet), $iguanaPayload);
        $response->assertRedirect(route('medical_records.show', $pet));

        $this->assertDatabaseHas('kardex_entries', [
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'animal_type' => 'iguana',
        ]);
    }

    public function test_kardex_entries_appear_in_medical_records_profile()
    {
        $doctor = User::factory()->create(['role' => 'doctor', 'lastname' => 'Doctor']);
        $owner = User::factory()->create(['role' => 'client', 'lastname' => 'Owner']);
        $pet = Pet::create([
            'user_id' => $owner->id,
            'name' => 'Luna',
            'species' => 'Perro',
            'gender' => 'female',
        ]);

        KardexEntry::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'entry_date' => Carbon::today(),
            'animal_type' => 'huron',
            'parameters' => [
                'frecuencia_cardiaca' => 210,
                'frecuencia_respiratoria' => 38,
                'temperatura' => 38.9,
                'glicemia' => 88,
                'hidratacion' => 95,
            ]
        ]);

        $response = $this->actingAs($doctor)->get(route('medical_records.show', $pet));
        $response->assertOk();

        // Check for specific labels and values
        $response->assertSee('Kardex Clínico (Mascotas Exóticas)');
        $response->assertSee('frecuencia cardiaca');
        $response->assertSee('210');
        $response->assertSee('lpm');
        $response->assertSee('frecuencia respiratoria');
        $response->assertSee('38');
        $response->assertSee('rpm');
        $response->assertSee('temperatura');
        $response->assertSee('38.9');
        $response->assertSee('°C');
        $response->assertSee('glicemia');
        $response->assertSee('88');
        $response->assertSee('mg/dL');
        $response->assertSee('hidratacion');
        $response->assertSee('95');
        $response->assertSee('%');
    }

    public function test_unauthorized_users_cannot_access_kardex_routes()
    {
        $client = User::factory()->create(['role' => 'client', 'lastname' => 'Client']);
        $owner = User::factory()->create(['role' => 'client', 'lastname' => 'Owner']);
        $pet = Pet::create([
            'user_id' => $owner->id,
            'name' => 'Luna',
            'species' => 'Perro',
            'gender' => 'female',
        ]);

        $payload = [
            'entry_date' => Carbon::today()->toDateString(),
            'animal_type' => 'huron',
            'parameters' => [
                'frecuencia_cardiaca' => 200,
                'frecuencia_respiratoria' => 30,
            ]
        ];

        // 1. Unauthorized client cannot store
        $response = $this->actingAs($client)->post(route('kardex.store', $pet), $payload);
        $response->assertStatus(403);

        // Create entry directly to check deletion permissions
        $doctor = User::factory()->create(['role' => 'doctor', 'lastname' => 'Doctor']);
        $entry = KardexEntry::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'entry_date' => Carbon::today(),
            'animal_type' => 'huron',
            'parameters' => ['fc' => 200]
        ]);

        // 2. Unauthorized client cannot delete
        $response = $this->actingAs($client)->delete(route('kardex.destroy', $entry));
        $response->assertStatus(403);
    }

    public function test_doctor_can_delete_kardex_entry()
    {
        $doctor = User::factory()->create(['role' => 'doctor', 'lastname' => 'Doctor']);
        $owner = User::factory()->create(['role' => 'client', 'lastname' => 'Owner']);
        $pet = Pet::create([
            'user_id' => $owner->id,
            'name' => 'Luna',
            'species' => 'Perro',
            'gender' => 'female',
        ]);

        $entry = KardexEntry::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'entry_date' => Carbon::today(),
            'animal_type' => 'huron',
            'parameters' => ['fc' => 200]
        ]);

        $response = $this->actingAs($doctor)->delete(route('kardex.destroy', $entry));
        $response->assertRedirect(route('medical_records.show', $pet));

        $this->assertDatabaseMissing('kardex_entries', [
            'id' => $entry->id,
        ]);
    }
}
