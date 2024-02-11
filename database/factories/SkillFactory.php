<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        return [
            'skill' => $this->faker->word,
            // Assuming the 'value' column exists in the 'skills' table
            'value' => rand(0,  100),
        ];
    }
}
