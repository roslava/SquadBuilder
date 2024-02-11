<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Player extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'position'];

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'player_skill')->withPivot('value');
    }
}
