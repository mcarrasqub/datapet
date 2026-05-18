<?php

namespace Tests\Feature;

use App\Models\AdoptionRequest;
use App\Models\Appointment;
use App\Models\KardexEntry;
use App\Models\MedicalExam;
use App\Models\MedicalFormula;
use App\Models\MedicalOrder;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use App\Models\Vaccination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_pet_getters_and_setters(): void
    {
        $pet = new Pet;

        $pet->setUserId(1);
        $this->assertEquals(1, $pet->getUserId());

        $pet->setName('Boby');
        $this->assertEquals('Boby', $pet->getName());

        $pet->setSpecies('Perro');
        $this->assertEquals('Perro', $pet->getSpecies());

        $pet->setBreed('Pug');
        $this->assertEquals('Pug', $pet->getBreed());

        $pet->setAge(5);
        $this->assertEquals(5, $pet->getAge());

        $pet->setGender('male');
        $this->assertEquals('male', $pet->getGender());

        $pet->setWeight(10.5);
        $this->assertEquals(10.5, $pet->getWeight());

        $pet->setPhoto('path/to/photo.jpg');
        $this->assertEquals('path/to/photo.jpg', $pet->getPhoto());

        $pet->setColor('Negro');
        $this->assertEquals('Negro', $pet->getColor());

        $pet->setSize('Pequeño');
        $this->assertEquals('Pequeño', $pet->getSize());

        $pet->setReproductiveStatus('Castrado');
        $this->assertEquals('Castrado', $pet->getReproductiveStatus());

        $pet->setIsDeceased(true);
        $this->assertTrue($pet->getIsDeceased());

        $pet->setEmotionalSupport(true);
        $this->assertTrue($pet->getEmotionalSupport());

        $pet->setServiceAnimal(true);
        $this->assertTrue($pet->getServiceAnimal());

        $pet->setDiet('Premium');
        $this->assertEquals('Premium', $pet->getDiet());

        $pet->setDietQuantity('1 taza');
        $this->assertEquals('1 taza', $pet->getDietQuantity());

        $pet->setDietFrequency('2 veces');
        $this->assertEquals('2 veces', $pet->getDietFrequency());

        $pet->setHousing('Casa');
        $this->assertEquals('Casa', $pet->getHousing());

        $pet->setBathFrequency('Mensual');
        $this->assertEquals('Mensual', $pet->getBathFrequency());

        $pet->setBathProducts('Champú');
        $this->assertEquals('Champú', $pet->getBathProducts());

        $pet->setOtherPets('Ninguna');
        $this->assertEquals('Ninguna', $pet->getOtherPets());

        $pet->setLastHeat('Hace 1 mes');
        $this->assertEquals('Hace 1 mes', $pet->getLastHeat());

        $pet->setAvailableForAdoption(true);
        $this->assertTrue($pet->getAvailableForAdoption());

        $pet->setAdoptionDescription('Lindo perrito');
        $this->assertEquals('Lindo perrito', $pet->getAdoptionDescription());
    }

    public function test_pet_relationships(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $doctor = User::factory()->create(['role' => 'doctor']);

        $pet = Pet::factory()->create(['user_id' => $client->id]);

        $this->assertInstanceOf(User::class, $pet->owner);

        MedicalRecord::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'visited_at' => now(),
            'reason' => 'Control',
            'diagnosis' => 'Sano',
        ]);
        $this->assertCount(1, $pet->medicalRecords);

        MedicalExam::create([
            'pet_id' => $pet->id,
            'uploaded_by' => $client->id,
            'title' => 'Radiografia',
            'file_path' => 'dummy.pdf',
            'original_name' => 'dummy.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'uploaded_at' => now(),
        ]);
        $this->assertCount(1, $pet->medicalExams);

        Vaccination::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'vaccine_type' => 'Rabia',
            'vaccinated_at' => now(),
            'next_due_date' => now()->addYear(),
        ]);
        $this->assertCount(1, $pet->vaccinations);

        AdoptionRequest::create([
            'pet_id' => $pet->id,
            'user_id' => $client->id,
            'full_name' => 'Carlos',
            'phone' => '123',
            'status' => 'pending',
        ]);
        $this->assertCount(1, $pet->adoptionRequests);

        Appointment::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'date' => now()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'status' => 'scheduled',
            'reason' => 'Consulta',
        ]);
        $this->assertCount(1, $pet->appointments);

        KardexEntry::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'entry_date' => now()->toDateString(),
            'animal_type' => 'dog',
            'parameters' => json_encode(['weight' => 5.5, 'procedure' => 'Test']),
        ]);
        $this->assertCount(1, $pet->kardexEntries);

        MedicalFormula::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'formula_date' => now()->toDateString(),
            'medications' => [['name' => 'A', 'dosage' => '1', 'frequency' => '2', 'duration' => '3']],
        ]);
        $this->assertCount(1, $pet->medicalFormulas);

        MedicalOrder::create([
            'pet_id' => $pet->id,
            'doctor_id' => $doctor->id,
            'order_date' => now()->toDateString(),
            'order_type' => 'Sangre',
            'description' => 'Ayuno',
        ]);
        $this->assertCount(1, $pet->medicalOrders);

        $this->assertIsInt($pet->getId());
    }
}
