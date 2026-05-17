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
            'color' => fake()->safeColorName(),
            'size' => fake()->randomElement(['Pequeña', 'Mediana', 'Grande']),
            'reproductive_status' => fake()->randomElement(['Esterilizado', 'Castrado', 'Entero']),
            'is_deceased' => false,
            'emotional_support' => fake()->boolean(10),
            'service_animal' => fake()->boolean(5),
            'diet' => fake()->sentence(),
            'diet_quantity' => '50g al día',
            'diet_frequency' => '2 veces al día',
            'housing' => fake()->sentence(),
            'bath_frequency' => 'Cada mes',
            'bath_products' => 'Champú neutro',
            'other_pets' => 'Ninguna',
            'last_heat' => 'N/A',
        ];
    }
}
