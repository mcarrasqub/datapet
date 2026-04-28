<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo AdoptionRequest
 *
 * Atributos:
 * - id: int - Identificador único
 * - pet_id: int - ID de la mascota
 * - user_id: int|null - ID del usuario (si está autenticado)
 * - full_name: string - Nombre completo del solicitante
 * - phone: string - Teléfono del solicitante
 * - experience: string|null - Experiencia con animales exóticos
 * - status: string - Estado (pending, approved, rejected)
 * - admin_notes: string|null - Notas del administrador
 * - created_at: timestamp - Fecha de creación
 * - updated_at: timestamp - Fecha de última actualización
 */
class AdoptionRequest extends Model
{
  protected $fillable = [
    'pet_id',
    'user_id',
    'full_name',
    'phone',
    'experience',
    'status',
    'admin_notes',
  ];

  protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  public function pet(): BelongsTo
  {
    return $this->belongsTo(Pet::class, 'pet_id');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  // Getters
  public function getId(): int
  {
    return $this->attributes['id'];
  }

  public function getPetId(): int
  {
    return $this->attributes['pet_id'];
  }

  public function getUserId(): ?int
  {
    return $this->attributes['user_id'] ?? null;
  }

  public function getFullName(): string
  {
    return $this->attributes['full_name'];
  }

  public function getPhone(): string
  {
    return $this->attributes['phone'];
  }

  public function getExperience(): ?string
  {
    return $this->attributes['experience'] ?? null;
  }

  public function getStatus(): string
  {
    return $this->attributes['status'];
  }

  public function getAdminNotes(): ?string
  {
    return $this->attributes['admin_notes'] ?? null;
  }

  // Setters
  public function setPetId(int $petId): void
  {
    $this->attributes['pet_id'] = $petId;
  }

  public function setUserId(?int $userId): void
  {
    $this->attributes['user_id'] = $userId;
  }

  public function setFullName(string $fullName): void
  {
    $this->attributes['full_name'] = $fullName;
  }

  public function setPhone(string $phone): void
  {
    $this->attributes['phone'] = $phone;
  }

  public function setExperience(?string $experience): void
  {
    $this->attributes['experience'] = $experience;
  }

  public function setStatus(string $status): void
  {
    $this->attributes['status'] = $status;
  }

  public function setAdminNotes(?string $adminNotes): void
  {
    $this->attributes['admin_notes'] = $adminNotes;
  }
}