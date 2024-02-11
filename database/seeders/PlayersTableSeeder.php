<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\Skill;
use Faker\Factory as Faker;

class PlayersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Use the Player factory to create   50 players
        Player::factory()->count(50)->create()->each(function ($player) use ($faker) {
            // Get a random subset of skills
            $skills = Skill::all()->random(rand(1, 5));

            // Prepare an array to hold the skill IDs and their values
            $skillValues = [];
            foreach ($skills as $skill) {
                $skillValues[$skill->id] = ['value' => $faker->numberBetween(1, 100)];
            }

            // Sync the skills with the player, ensuring no duplicates
            $player->skills()->sync($skillValues);
        });
    }
}
