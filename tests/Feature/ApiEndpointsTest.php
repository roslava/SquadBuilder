<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Player;
use App\Models\Skill;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a player.
     *
     * @return void
     */
    public function testCreatingAPlayer()
    {
        $payload = [
            'name' => 'John Doe',
            'position' => 'midfielder',
            'playerSkills' => [
                ['skill' => 'speed', 'value' =>   80],
                ['skill' => 'strength', 'value' =>   70],
            ],
        ];

        $response = $this->postJson('/api/player', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'name' => $payload['name'],
                'position' => $payload['position'],
            ]);

        $this->assertDatabaseHas('players', [
            'name' => $payload['name'],
            'position' => $payload['position'],
        ]);
    }

    /**
     * Test updating a player's position.
     *
     * @return void
     */
    public function testUpdatingPlayerPosition()
    {
        $player = Player::factory()->create();

        $updatedData = [
            'position' => 'forward',
        ];

        $response = $this->putJson("/api/player/{$player->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $player->id,
                'position' => $updatedData['position'],
            ]);

        $this->assertDatabaseHas('players', [
            'id' => $player->id,
            'position' => $updatedData['position'],
        ]);
    }

    /**
     * Test deleting a player.
     *
     * @return void
     */
    public function testDeletingAPlayer()
    {
        $player = Player::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer SkFabTZibXE1aE14ckpQUUxHc2dnQ2RzdlFRTTM2NFE2cGI4d3RQNjZmdEFITmdBQkE=',
        ])->delete("/api/player/{$player->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('players', [
            'id' => $player->id,
        ]);
    }



    /**
     * Test listing players.
     *
     * @return void
     */
    public function testListingPlayers()
    {
        $players = Player::factory()->count(3)->create();

        $response = $this->get('/api/player');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'position'],
            ]);
    }

    /**
     * Test selecting the best team.
     *
     * @return void
     */
    /**
     * Test selecting the best team.
     *
     * @return void
     */
    public function testSelectingBestTeam()
    {
        // Create players with various skills
        $player1 = Player::factory()->create(['name' => 'Player   1', 'position' => 'midfielder']);
        $player2 = Player::factory()->create(['name' => 'Player   2', 'position' => 'midfielder']);
        $player3 = Player::factory()->create(['name' => 'Player   3', 'position' => 'defender']);
        $player4 = Player::factory()->create(['name' => 'Player   4', 'position' => 'defender']);

        // Attach skills to players
        $skill1 = Skill::factory()->create(['skill' => 'speed', 'value' =>   80]);
        $skill2 = Skill::factory()->create(['skill' => 'speed', 'value' =>   90]);
        $skill3 = Skill::factory()->create(['skill' => 'strength', 'value' =>   70]);
        $skill4 = Skill::factory()->create(['skill' => 'stamina', 'value' =>   2]);

        $player1->skills()->attach($skill1->id, ['value' =>   80]);
        $player2->skills()->attach($skill2->id, ['value' =>   90]);
        $player3->skills()->attach($skill3->id, ['value' =>   70]);
        $player4->skills()->attach($skill4->id, ['value' =>   2]);

        // Define the requirements for the best team
        $requirements = [
            [
                'position' => 'midfielder',
                'mainSkill' => 'speed',
                'numberOfPlayers' =>   1
            ],
            [
                'position' => 'defender',
                'mainSkill' => 'strength',
                'numberOfPlayers' =>   2
            ]
        ];

        // Make a POST request to the team selection endpoint
        $response = $this->postJson('/api/team/process', $requirements);

        // Assert the response indicates success and the selected team is as expected
        $response->assertStatus(200)
            ->assertJsonPath('*.name', ['Player   2', 'Player   3'])
            ->assertJsonPath('*.position', ['midfielder', 'defender'])
            ->assertJsonPath('*.playerSkills.*.skill', ['speed', 'strength'])
            ->assertJsonPath('*.playerSkills.*.value', [90,  70]);
    }




}
