<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('player_imports', function (Blueprint $table) {
            $table->id();
            $table->string('world');
            $table->unsignedInteger('player_id');
            $table->string('name');
            $table->string('race')->nullable();
            $table->unsignedInteger('clan_id')->nullable();
            $table->string('profession')->nullable();
            $table->unsignedInteger('xp');
            $table->unsignedInteger('soul_xp');
            $table->unsignedInteger('total_xp');

            $table->unique(['world', 'player_id']);
        });
    }
};
