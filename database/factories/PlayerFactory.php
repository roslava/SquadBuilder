<?php

namespace Database\Factories;

use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'position' => $this->faker->randomElement(['defender', 'midfielder', 'forward']),
        ];
    }

    // Optional: Method to attach skills to a player
    public function withSkills(array $skills = []): self
    {
        return $this->afterCreating(function (Player $player) use ($skills) {
            foreach ($skills as $skill) {
                $player->skills()->attach($skill, ['value' => rand(0, 100)]);
            }
        });
    }
}
