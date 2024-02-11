<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;

class SkillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $availableSkills = ['defense', 'attack', 'speed', 'strength', 'stamina'];

        // Create new skills
        foreach ($availableSkills as $skillName) {
            Skill::firstOrCreate(['skill' => $skillName]);
        }
    }
}
