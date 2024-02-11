<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SkillController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'position' => 'required|in:defender,midfielder,forward',
            'playerSkills' => 'required|array',
            'playerSkills.*.skill' => ['required', Rule::in(['defense', 'attack', 'speed', 'strength', 'stamina'])],
            'playerSkills.*.value' => 'required|integer|min:0|max:100',
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create the player record
            $player = Player::create([
                'name' => $validatedData['name'],
                'position' => $validatedData['position'],
            ]);

            // Associate skills with the player
            foreach ($validatedData['playerSkills'] as $skillData) {
                $skill = Skill::firstOrCreate(['skill' => $skillData['skill']]);
                $player->skills()->attach($skill->id, ['value' => $skillData['value']]);
            }

            // Commit the transaction
            DB::commit();

            // Return the created player data
            return response()->json($player->load('skills'), 201);
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Handle the exception (log the error, return a response, etc.)
            return response()->json(['message' => 'An error occurred while creating the player.'], 500);
        }
    }
}
