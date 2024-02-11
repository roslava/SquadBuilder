<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Skill;
use App\Services\TeamSelectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
//        return var_dump('here');
        // Retrieve all players and eager load their skills
        $players = Player::with('skills')->get();

        // Transform the players collection to include the playerSkills array
        $transformedPlayers = $players->map(function ($player) {
            // Map over the skills to create the playerSkills array
            $playerSkills = $player->skills->map(function ($skill) use ($player) {
                return [
                    'id' => $skill->id,
                    'skill' => $skill->skill,
                    'value' => $skill->pivot->value,
                    'playerId' => $player->id,
                ];
            });

            // Return the transformed player data
            return [
                'id' => $player->id,
                'name' => $player->name,
                'position' => $player->position,
                'playerSkills' => $playerSkills,
            ];
        });

        // Return the transformed players as a JSON response
        return response()->json($transformedPlayers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the incoming request data
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

            // Prepare an array to hold the playerSkills data
            $playerSkillsData = [];

            // Associate playerSkills with the player
            foreach ($validatedData['playerSkills'] as $skillData) {
                $skill = Skill::firstOrCreate(['skill' => $skillData['skill']]);
                $player->skills()->attach($skill->id, ['value' => $skillData['value']]);

                // Add the skill data to the playerSkillsData array
                $playerSkillsData[] = [
                    'id' => $skill->id,
                    'skill' => $skill->skill,
                    'value' => $skillData['value'],
                    'playerId' => $player->id,
                ];
            }

            // Commit the transaction
            DB::commit();

            // Return the created player data with playerSkills
            return response()->json([
                'id' => $player->id,
                'name' => $player->name,
                'position' => $player->position,
                'playerSkills' => $playerSkillsData,
            ],   201); //   201 Created status code
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Log the error and return a generic error response
            Log::error('Failed to create player: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while creating the player.'],   500);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $player = Player::findOrFail($id);
        return response()->json($player);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $player = Player::findOrFail($id);

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'sometimes|required|max:255',
            'position' => 'sometimes|required|in:defender,midfielder,forward',
            'playerSkills' => 'sometimes|array',
            'playerSkills.*.skill' => ['sometimes', 'required', Rule::in(['defense', 'attack', 'speed', 'strength', 'stamina'])],
            'playerSkills.*.value' => 'sometimes|required|integer|min:0|max:100',
        ]);

        // Update the player's attributes
        $player->update($validatedData);

        // If playerSkills are included in the request, update them as well
        if ($request->has('playerSkills')) {
            // Prepare an array to hold the updated playerSkills data
            $playerSkillsData = [];

            // Process each skill in the playerSkills array
            foreach ($validatedData['playerSkills'] as $skillData) {
                $skill = Skill::firstOrCreate(['skill' => $skillData['skill']]);
                $player->skills()->syncWithoutDetaching([$skill->id => ['value' => $skillData['value']]]);

                // Add the skill data to the playerSkillsData array
                $playerSkillsData[] = [
                    'id' => $skill->id,
                    'skill' => $skill->skill,
                    'value' => $skillData['value'],
                    'playerId' => $player->id,
                ];
            }

            // Return the updated player data with playerSkills
            return response()->json([
                'id' => $player->id,
                'name' => $player->name,
                'position' => $player->position,
                'playerSkills' => $playerSkillsData,
            ]);
        } else {
            // If no playerSkills are provided, just return the updated player data
            return response()->json([
                'id' => $player->id,
                'name' => $player->name,
                'position' => $player->position,
            ]);
        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        // Since we are not using Sanctum for authentication, we don't need to check for a user or token.
        // We can proceed directly to finding and deleting the player.

        // Find the player by ID
        $player = Player::findOrFail($id);

        // Delete the player
        $player->delete();

        // Return a successful response
        return response()->json(null,   204);
    }

    protected TeamSelectionService $teamSelectionService;

    public function __construct(TeamSelectionService $teamSelectionService)
    {
        $this->teamSelectionService = $teamSelectionService;
    }

    /**
     * Select the best team based on the provided requirements.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function selectBestTeam(Request $request): JsonResponse
    {
        try{
            $request->validate([
                '*.position' => 'sometimes|required|max:255',
                '*.mainSkill' => 'sometimes|required|max:255',
                '*.numberOfPlayers' => 'sometimes|required|integer',
            ]);

            // Delegate the work to the TeamSelectionService
            $selectedPlayers = $this->teamSelectionService->selectBestTeamFromRequirements($request->all());

            // Transform the selected players to include the playerSkills array
            $transformedSelectedPlayers = $selectedPlayers->map(function ($player) {
                // Map over the skills to create the playerSkills array
                $playerSkills = $player->skills->map(function ($skill) {
                    return [
                        'skill' => $skill->skill,
                        'value' => $skill->pivot->value,
                    ];
                });

                // Return the transformed player data
                return [
                    'name' => $player->name,
                    'position' => $player->position,
                    'playerSkills' => $playerSkills,
                ];
            });

            // Return the transformed selected players as a JSON response
            return response()->json($transformedSelectedPlayers);
        }catch (\Throwable $exception){
            var_dump($exception->getMessage()); exit;
        }

    }


}
