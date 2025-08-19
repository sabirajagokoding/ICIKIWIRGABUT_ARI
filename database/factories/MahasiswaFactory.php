<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MahasiswaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nim' => $this->faker->unique()->numerify('2025####'), 
            'nama' => $this->faker->name(),
            'kelas' => $this->faker->randomElement(['A', 'B', 'C']),
            'status' => $this->faker->randomElement([0]),
        ];
    }
}
