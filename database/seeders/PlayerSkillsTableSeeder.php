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

        // Use the Player factory to create  50 players
        Player::factory()->count(50)->create()->each(function ($player) use ($faker) {
            // Assign at least one skill to the player
            $skill = Skill::all()->random();
            $player->skills()->attach($skill->id, ['value' => $faker->numberBetween(1,  100)]);

            // Optionally, assign additional skills with random values
            $additionalSkillsCount = $faker->numberBetween(0,  4);
            for ($i =  0; $i < $additionalSkillsCount; $i++) {
                $additionalSkill = Skill::all()->except($skill->id)->random();
                $player->skills()->attach($additionalSkill->id, ['value' => $faker->numberBetween(1,  100)]);
            }
        });
    }
}
