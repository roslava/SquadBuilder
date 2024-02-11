<?php

namespace App\Services;

use App\Models\Player;
use Illuminate\Support\Collection;

class TeamSelectionService
{
    /**
     * Select the best team based on the provided requirements.
     *
     * @param array $requirements
     * @return Collection
     */
    public function selectBestTeamFromRequirements(array $requirements): Collection
    {
        $selectedPlayers = collect();
        $usedPlayerIds = [];

        foreach ($requirements as $requirement) {
            $players = Player::whereHas('skills', function ($query) use ($requirement) {
                $query->where('skill', $requirement['mainSkill']);
            })
                ->where('position', $requirement['position'])
                ->join('player_skill', 'players.id', '=', 'player_skill.player_id')
                ->join('skills', 'player_skill.skill_id', '=', 'skills.id')
                ->select('players.*')
                ->orderByDesc('player_skill.value') // Corrected order by clause
                ->take($requirement['numberOfPlayers'])
                ->get();

            if ($players->isEmpty()) {
                $players = Player::where('position', $requirement['position'])
                    ->join('player_skill', 'players.id', '=', 'player_skill.player_id')
                    ->join('skills', 'player_skill.skill_id', '=', 'skills.id')
                    ->select('players.*')
                    ->orderByDesc('player_skill.value') // Corrected order by clause
                    ->take($requirement['numberOfPlayers'])
                    ->get();
            }

            foreach ($players as $player) {
                if (!in_array($player->id, $usedPlayerIds)) {
                    $selectedPlayers->push($player);
                    $usedPlayerIds[] = $player->id;
                }
            }
        }

        // Check if the number of selected players meets the requirements
        if ($selectedPlayers->count() < count($requirements)) {
            throw new \Exception("Insufficient number of players for position: {$requirement['position']}");
        }

        return $selectedPlayers;
    }
}
