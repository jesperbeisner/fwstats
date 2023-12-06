<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('removed_players', function (Blueprint $table) {
            $table->id();
            $table->integer('player_id');
            $table->string('player_world');
            $table->dateTime('created_at');

            $table->index(['player_id', 'player_world']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('removed_players');
    }
};
