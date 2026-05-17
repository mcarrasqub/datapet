<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo Pet
 *
 * Atributos:
 * - id: int - Identificador único
 * - user_id: int - ID del usuario propietario
 * - name: string - Nombre de la mascota
 * - species: string - Especie (Perro, Gato, etc.)
 * - breed: string|null - Raza
 * - age: integer|null - Edad en años
 * - gender: string - Género (male, female, unknown)
 * - weight: decimal|null - Peso en kilogramos
 * - photo: string|null - Ruta de la foto
 * - notes: string|null - Notas adicionales
 * - available_for_adoption: boolean - Disponible para adopción
 * - adoption_description: string|null - Descripción para adopción
 * - created_at: timestamp - Fecha de creación
 * - updated_at: timestamp - Fecha de última actualización
 */
class Pet extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'name',
    'species',
    'breed',
    'age',
    'gender',
    'weight',
    'photo',
    'color',
    'size',
    'reproductive_status',
    'is_deceased',
    'emotional_support',
    'service_animal',
    'diet',
    'diet_quantity',
    'diet_frequency',
    'housing',
    'bath_frequency',
    'bath_products',
    'other_pets',
    'last_heat',
    'available_for_adoption',
    'adoption_description',
  ];

  protected $casts = [
    'age' => 'integer',
    'weight' => 'decimal:2',
    'available_for_adoption' => 'boolean',
    'is_deceased' => 'boolean',
    'emotional_support' => 'boolean',
    'service_animal' => 'boolean',
  ];

  public function owner(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function medicalRecords(): HasMany
  {
    return $this->hasMany(MedicalRecord::class, 'pet_id');
  }

  public function medicalExams(): HasMany
  {
    return $this->hasMany(MedicalExam::class, 'pet_id');
  }

  public function vaccinations(): HasMany
  {
    return $this->hasMany(Vaccination::class, 'pet_id');
  }

  public function adoptionRequests(): HasMany
  {
    return $this->hasMany(AdoptionRequest::class, 'pet_id');
  }

  public function appointments(): HasMany
  {
    return $this->hasMany(Appointment::class, 'pet_id');
  }

  public function kardexEntries(): HasMany
  {
    return $this->hasMany(KardexEntry::class, 'pet_id');
  }

  public function medicalFormulas(): HasMany
  {
    return $this->hasMany(MedicalFormula::class, 'pet_id');
  }

  public function medicalOrders(): HasMany
  {
    return $this->hasMany(MedicalOrder::class, 'pet_id');
  }

  // Getters
  public function getId(): int
  {
    return $this->attributes['id'];
  }

  public function getUserId(): int
  {
    return $this->attributes['user_id'];
  }

  public function getName(): string
  {
    return $this->attributes['name'];
  }

  public function getSpecies(): string
  {
    return $this->attributes['species'];
  }

  public function getBreed(): ?string
  {
    return $this->attributes['breed'] ?? null;
  }

  public function getAge(): ?int
  {
    return $this->attributes['age'] ?? null;
  }

  public function getGender(): string
  {
    return $this->attributes['gender'];
  }

  public function getWeight(): ?float
  {
    return $this->attributes['weight'] ?? null;
  }

  public function getPhoto(): ?string
  {
    return $this->attributes['photo'] ?? null;
  }

  public function getColor(): ?string
  {
    return $this->attributes['color'] ?? null;
  }

  public function getSize(): ?string
  {
    return $this->attributes['size'] ?? null;
  }

  public function getReproductiveStatus(): ?string
  {
    return $this->attributes['reproductive_status'] ?? null;
  }

  public function getIsDeceased(): bool
  {
    return (bool) ($this->attributes['is_deceased'] ?? false);
  }

  public function getEmotionalSupport(): bool
  {
    return (bool) ($this->attributes['emotional_support'] ?? false);
  }

  public function getServiceAnimal(): bool
  {
    return (bool) ($this->attributes['service_animal'] ?? false);
  }

  public function getDiet(): ?string
  {
    return $this->attributes['diet'] ?? null;
  }

  public function getDietQuantity(): ?string
  {
    return $this->attributes['diet_quantity'] ?? null;
  }

  public function getDietFrequency(): ?string
  {
    return $this->attributes['diet_frequency'] ?? null;
  }

  public function getHousing(): ?string
  {
    return $this->attributes['housing'] ?? null;
  }

  public function getBathFrequency(): ?string
  {
    return $this->attributes['bath_frequency'] ?? null;
  }

  public function getBathProducts(): ?string
  {
    return $this->attributes['bath_products'] ?? null;
  }

  public function getOtherPets(): ?string
  {
    return $this->attributes['other_pets'] ?? null;
  }

  public function getLastHeat(): ?string
  {
    return $this->attributes['last_heat'] ?? null;
  }

  public function getAvailableForAdoption(): bool
  {
    return $this->attributes['available_for_adoption'] ?? false;
  }

  public function getAdoptionDescription(): ?string
  {
    return $this->attributes['adoption_description'] ?? null;
  }

  // Setters
  public function setUserId(int $userId): void
  {
    $this->attributes['user_id'] = $userId;
  }

  public function setName(string $name): void
  {
    $this->attributes['name'] = $name;
  }

  public function setSpecies(string $species): void
  {
    $this->attributes['species'] = $species;
  }

  public function setBreed(?string $breed): void
  {
    $this->attributes['breed'] = $breed;
  }

  public function setAge(?int $age): void
  {
    $this->attributes['age'] = $age;
  }

  public function setGender(string $gender): void
  {
    $this->attributes['gender'] = $gender;
  }

  public function setWeight(?float $weight): void
  {
    $this->attributes['weight'] = $weight;
  }

  public function setPhoto(?string $photo): void
  {
    $this->attributes['photo'] = $photo;
  }

  public function setColor(?string $color): void
  {
    $this->attributes['color'] = $color;
  }

  public function setSize(?string $size): void
  {
    $this->attributes['size'] = $size;
  }

  public function setReproductiveStatus(?string $reproductiveStatus): void
  {
    $this->attributes['reproductive_status'] = $reproductiveStatus;
  }

  public function setIsDeceased(bool $isDeceased): void
  {
    $this->attributes['is_deceased'] = $isDeceased;
  }

  public function setEmotionalSupport(bool $emotionalSupport): void
  {
    $this->attributes['emotional_support'] = $emotionalSupport;
  }

  public function setServiceAnimal(bool $serviceAnimal): void
  {
    $this->attributes['service_animal'] = $serviceAnimal;
  }

  public function setDiet(?string $diet): void
  {
    $this->attributes['diet'] = $diet;
  }

  public function setDietQuantity(?string $dietQuantity): void
  {
    $this->attributes['diet_quantity'] = $dietQuantity;
  }

  public function setDietFrequency(?string $dietFrequency): void
  {
    $this->attributes['diet_frequency'] = $dietFrequency;
  }

  public function setHousing(?string $housing): void
  {
    $this->attributes['housing'] = $housing;
  }

  public function setBathFrequency(?string $bathFrequency): void
  {
    $this->attributes['bath_frequency'] = $bathFrequency;
  }

  public function setBathProducts(?string $bathProducts): void
  {
    $this->attributes['bath_products'] = $bathProducts;
  }

  public function setOtherPets(?string $otherPets): void
  {
    $this->attributes['other_pets'] = $otherPets;
  }

  public function setLastHeat(?string $lastHeat): void
  {
    $this->attributes['last_heat'] = $lastHeat;
  }

  public function setAvailableForAdoption(bool $available): void
  {
    $this->attributes['available_for_adoption'] = $available;
  }

  public function setAdoptionDescription(?string $description): void
  {
    $this->attributes['adoption_description'] = $description;
  }
}