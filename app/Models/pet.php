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
    'notes'
  ];

  protected $casts = [
    'age' => 'integer',
    'weight' => 'decimal:2'
  ];

  public function owner(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function medicalRecords(): HasMany
  {
    return $this->hasMany(MedicalRecord::class, 'pet_id');
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

  public function getNotes(): ?string
  {
    return $this->attributes['notes'] ?? null;
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

  public function setNotes(?string $notes): void
  {
    $this->attributes['notes'] = $notes;
  }
}