<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // First, run the SkillsTableSeeder to populate the skills table
        $this->call([
            SkillsTableSeeder::class,
        ]);

        // Then, run the PlayersTableSeeder to populate the players table
        $this->call([
            PlayersTableSeeder::class,
        ]);
    }
}
