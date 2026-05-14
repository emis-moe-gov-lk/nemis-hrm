<?php

namespace Database\Factories;

use App\Models\Ethnicity;
use Illuminate\Database\Eloquent\Factories\Factory;

class EthnicityFactory extends Factory
{
    protected $model = Ethnicity::class;

    public function definition(): array
    {
        return [
            'ethnicity_id' => $this->faker->unique()->numerify('E##'),
            'ethnicity_name' => $this->faker->randomElement(['Sinhalese', 'Tamil', 'Moor', 'Burgher', 'Other']),
            'active_status' => 1,
        ];
    }
}
