<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MahasiswaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nim' => $this->faker->unique()->numerify($this->faker->randomElement(['222111###','222112###'])), 
            'nama' => $this->faker->name(),
            'kelas' => $this->faker->randomElement(['4SD1', '4SD2', '4SI1','4SI2','4SK1','4SK2','4SE1','4SE2','3D31','3S32','3D33']),
            'status' => $this->faker->randomElement([0]),
        ];
    }
}
