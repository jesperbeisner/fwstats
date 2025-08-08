<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('world');
            $table->unsignedInteger('player_id');
            $table->string('name');
            $table->string('race');
            $table->unsignedInteger('clan_id');
            $table->string('profession')->nullable();
            $table->unsignedInteger('xp');
            $table->unsignedInteger('soul_xp');
            $table->unsignedInteger('total_xp');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->unique(['world', 'player_id']);
            $table->index('total_xp');
            $table->index(['world', 'total_xp']);
        });
    }
};
