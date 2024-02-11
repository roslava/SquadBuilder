<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\Skill;
use App\Services\TeamSelectionService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamSelectionTest extends TestCase
{
    use RefreshDatabase;

    private TeamSelectionService $teamSelectionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamSelectionService = new TeamSelectionService();
    }

    /**
     * @throws Exception
     */
    public function testSelectBestTeamFromRequirements()
    {
        // Set up players and skills
        $player1 = Player::factory()->create(['name' => 'Player  1', 'position' => 'midfielder']);
        $player2 = Player::factory()->create(['name' => 'Player  2', 'position' => 'midfielder']);
        $player3 = Player::factory()->create(['name' => 'Player  3', 'position' => 'defender']);

        $skill1 = Skill::factory()->create(['skill' => 'speed', 'value' =>  80]);
        $skill2 = Skill::factory()->create(['skill' => 'speed', 'value' =>  90]);
        $skill3 = Skill::factory()->create(['skill' => 'strength', 'value' =>  70]);

        // Attach skills to players
        $player1->skills()->attach($skill1->id, ['value' =>  80]);
        $player2->skills()->attach($skill2->id, ['value' =>  90]);
        $player3->skills()->attach($skill3->id, ['value' =>  70]);

        // Define the requirements
        $requirements = [
            [
                'position' => 'midfielder',
                'mainSkill' => 'speed',
                'numberOfPlayers' =>  1
            ],
            [
                'position' => 'defender',
                'mainSkill' => 'strength',
                'numberOfPlayers' =>  1
            ]
        ];

        // Call the service method
        $selectedPlayers = $this->teamSelectionService->selectBestTeamFromRequirements($requirements);

        // Assertions
        $this->assertCount(2, $selectedPlayers);
        $this->assertEquals('Player  2', $selectedPlayers[0]->name); // Should select player with highest speed
        $this->assertEquals('Player  3', $selectedPlayers[1]->name); // Should select player with highest strength
    }
}
