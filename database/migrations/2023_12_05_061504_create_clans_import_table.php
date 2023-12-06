<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clans_import', function (Blueprint $table) {
            $table->integer('id');
            $table->string('world');
            $table->string('name');
            $table->string('shortcut')->nullable();
            $table->integer('leader_id')->nullable();
            $table->integer('co_leader_id')->nullable();

            $table->primary(['id', 'world']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clans_import');
    }
};
