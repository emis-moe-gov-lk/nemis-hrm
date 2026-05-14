<?php

namespace Database\Factories;

use App\Models\People;
use App\Models\Title;
use App\Models\GenderList;
use App\Models\Religion;
use App\Models\Ethnicity;
use App\Models\CivilStatus;
use App\Helpers\NicHelper;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeopleFactory extends Factory
{
    protected $model = People::class;

    /**
     * Generate a valid old-format NIC: YYDDDSSSSV
     * YY = birth year last 2 digits (70-99)
     * DDD = day of year (001-366)
     * SSSS = serial digits
     * Suffix = V
     */
    private function generateValidNic(): string
    {
        $yy   = str_pad($this->faker->numberBetween(70, 99), 2, '0', STR_PAD_LEFT);
        $ddd  = str_pad($this->faker->numberBetween(1, 366), 3, '0', STR_PAD_LEFT);
        $ssss = str_pad($this->faker->numberBetween(0, 9999), 4, '0', STR_PAD_LEFT);
        return $yy . $ddd . $ssss . 'V';
    }

    public function definition(): array
    {
        $fullName = $this->faker->name();
        $nic = $this->generateValidNic();

        return [
            // people_id is auto-generated in model boot
            'nic'              => $nic,
            'nic_hash'         => NicHelper::hash($nic),
            'title_id'         => Title::factory()->create()->title_id,
            'full_name'        => $fullName,
            'name_with_initials' => People::generateInitials($fullName),
            'gender_id'        => GenderList::factory()->create()->gender_id,
            'date_of_birth'    => $this->faker->date('Y-m-d', '2000-01-01'),
            'religion_id'      => Religion::factory()->create()->religion_id,
            'ethnicity_id'     => Ethnicity::factory()->create()->ethnicity_id,
            'civil_status_id'  => CivilStatus::factory()->create()->civil_status_id,
            'health_condition' => true,
            'email'            => $this->faker->unique()->safeEmail(),
            'phone'            => $this->faker->unique()->numerify('07########'),
            'address_line1'    => $this->faker->streetAddress(),
            'address_line2'    => $this->faker->city(),
            'active_status'    => true,
        ];
    }
}
