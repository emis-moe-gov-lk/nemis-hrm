<?php

namespace Database\Factories;

use App\Models\CivilStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class CivilStatusFactory extends Factory
{
    protected $model = CivilStatus::class;

    public function definition(): array
    {
        return [
            'civil_status_id' => $this->faker->unique()->numerify('CS##'),
            'civil_status_name' => $this->faker->randomElement(['Single', 'Married', 'Divorced', 'Widowed']),
            'active_status' => 1,
        ];
    }
}
