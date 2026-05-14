<?php

namespace Database\Factories;

use App\Models\GenderList;
use Illuminate\Database\Eloquent\Factories\Factory;

class GenderListFactory extends Factory
{
    protected $model = GenderList::class;

    public function definition(): array
    {
        return [
            'gender_id' => $this->faker->unique()->numerify('G##'),
            'gender_name' => $this->faker->randomElement(['Male', 'Female']),
            'active_status' => 1,
        ];
    }
}
