<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * ATTRIBUTES USER
     * this->attributes['id']
     * this->attributes['name']
     * this->attributes['lastname']
     * this->attributes['phone']
     * this->attributes['address']
     * this->attributes['specialty']
     * this->attributes['email']
     * this->attributes['email_verified_at']
     * this->attributes['password']
     * this->attributes['status']
     * this->attributes['role']
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'address',
        'email',
        'phone',
        'password',
        'status',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function uploadedMedicalExams()
    {
        return $this->hasMany(MedicalExam::class, 'uploaded_by');
    }

    public function doctorTasks()
    {
        return $this->hasMany(DoctorTask::class, 'doctor_id');
    }

    // Getters
    public function getId(): int
    {
        return $this->attributes['id'];
    }

    public function getName(): string
    {
        return $this->attributes['name'];
    }

    public function getLastname(): string
    {
        return $this->attributes['lastname'];
    }

    public function getPhone(): ?string
    {
        return $this->attributes['phone'] ?? null;
    }

    public function getAddress(): ?string
    {
        return $this->attributes['address'] ?? null;
    }

    public function getSpecialty(): ?string
    {
        return $this->attributes['specialty'] ?? null;
    }

    public function getEmail(): string
    {
        return $this->attributes['email'];
    }

    public function getStatus(): ?bool
    {
        return $this->attributes['status'] ?? null;
    }

    public function getRole(): string
    {
        return $this->attributes['role'];
    }

    public function getCreatedAt(): \Illuminate\Support\Carbon
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): \Illuminate\Support\Carbon
    {
        return $this->updated_at;
    }

    // Setters
    public function setName(string $name): void
    {
        $this->attributes['name'] = $name;
    }

    public function setLastname(string $lastname): void
    {
        $this->attributes['lastname'] = $lastname;
    }

    public function setPhone(?string $phone): void
    {
        $this->attributes['phone'] = $phone;
    }

    public function setAddress(?string $address): void
    {
        $this->attributes['address'] = $address;
    }

    public function setSpecialty(?string $specialty): void
    {
        $this->attributes['specialty'] = $specialty;
    }

    public function setEmail(string $email): void
    {
        $this->attributes['email'] = $email;
    }

    public function setStatus(?bool $status): void
    {
        $this->attributes['status'] = $status;
    }

    public function setRole(string $role): void
    {
        $this->attributes['role'] = $role;
    }

    // Scopes
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByClient($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('lastname', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('id', 'like', "%{$term}%");
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByPet($query, string $term)
    {
        return $query->whereHas('pets', function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('id', 'like', "%{$term}%");
        });
    }
}

