<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Player;
use App\Models\User;

class SkillControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test storing a new player with skills.
     *
     * @return void
     */
    public function testStoreMethod()
    {
        // Arrange: Prepare the request payload
        $payload = [
            'name' => 'John Doe',
            'position' => 'midfielder',
            'playerSkills' => [
                ['skill' => 'speed', 'value' =>   80],
                ['skill' => 'strength', 'value' =>   70],
            ],
        ];

        // Act: Authenticate the user and send a POST request to the store method
        $response = $this->actingAs($this->authenticatedUser(), 'sanctum')
            ->postJson('/api/skills', $payload);

        // Assert: Check that the response is OK and contains the expected data
        $response->assertStatus(201);
        $response->assertJson([
            'name' => $payload['name'],
            'position' => $payload['position'],
        ]);

        // Assert: Check that the player was stored in the database
        $this->assertDatabaseHas('players', [
            'name' => $payload['name'],
            'position' => $payload['position'],
        ]);

        // Assert: Check that the skills were associated with the player
        $player = Player::where('name', $payload['name'])->first();
        $this->assertNotNull($player);
        $this->assertEquals(2, $player->skills()->count());

        // Correct the assertions to check the pivot table
        $this->assertDatabaseHas('player_skill', [
            'skill_id' => function ($query) {
                $query->select('id')->from('skills')->where('skill', 'speed');
            },
            'player_id' => $player->id,
            'value' =>   80,
        ]);
        $this->assertDatabaseHas('player_skill', [
            'skill_id' => function ($query) {
                $query->select('id')->from('skills')->where('skill', 'strength');
            },
            'player_id' => $player->id,
            'value' =>   70,
        ]);
    }

    /**
     * Get an authenticated user instance.
     *
     * @return \App\Models\User
     */
    private function authenticatedUser()
    {
        // Create and return an authenticated user instance
        // Replace User::factory() with the appropriate factory for your User model
        return User::factory()->create();
    }
}
