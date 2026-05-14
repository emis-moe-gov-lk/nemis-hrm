<?php

namespace Database\Factories;

use App\Models\Religion;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReligionFactory extends Factory
{
    protected $model = Religion::class;

    public function definition(): array
    {
        return [
            'religion_id' => $this->faker->unique()->numerify('R##'),
            'religion_name' => $this->faker->randomElement(['Buddhism', 'Hinduism', 'Islam', 'Christianity', 'Other']),
            'active_status' => 1,
        ];
    }
}
