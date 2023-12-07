<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('removed_players_import', function (Blueprint $table) {
            $table->integer('player_id');
            $table->string('player_world');

            $table->index(['player_id', 'player_world']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('removed_players_import');
    }
};
