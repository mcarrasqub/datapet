<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pet>
 */
class PetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $species = fake()->randomElement(['Perro', 'Gato', 'Conejo', 'Hamster', 'Pajaro']);
        $breeds = [
            'Perro' => ['Labrador', 'Pastor Alemán', 'Bulldog', 'Poodle', 'Beagle'],
            'Gato' => ['Persa', 'Siamés', 'Bengala', 'Maine Coon', 'Ragdoll'],
            'Conejo' => ['Holland Lop', 'Flemish Giant', 'Angora', 'Rex'],
            'Hamster' => ['Sirio', 'Enano', 'Roborovski'],
            'Pajaro' => ['Canario', 'Periquito', 'Loro', 'Cotorra'],
        ];

        return [
            'name' => fake()->firstName(),
            'species' => $species,
            'breed' => fake()->randomElement($breeds[$species] ?? ['Raza desconocida']),
            'age' => fake()->numberBetween(0, 15),
            'gender' => fake()->randomElement(['male', 'female', 'unknown']),
            'weight' => fake()->randomFloat(2, 1, 50),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
