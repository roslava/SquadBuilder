<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Skill extends Model
{
    use HasFactory;

    protected $fillable = ['skill', 'value'];

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'player_skill')->withPivot('value');
    }
}
