<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerSkillTable extends Migration
{
    public function up(): void
    {
        Schema::create('player_skill', function (Blueprint $table) {
            $table->unsignedBigInteger('player_id');
            $table->unsignedBigInteger('skill_id');
            $table->integer('value');

            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');

            $table->primary(['player_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_skill');
    }
};
