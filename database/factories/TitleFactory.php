<?php
 
namespace Database\Factories;
 
use App\Models\Title;
use Illuminate\Database\Eloquent\Factories\Factory;
 
class TitleFactory extends Factory
{
    protected $model = Title::class;
 
    public function definition(): array
    {
        return [
            'title_id' => $this->faker->unique()->numerify('T##'),
            'title_name' => $this->faker->title(),
            'active_status' => 1,
        ];
    }
}
