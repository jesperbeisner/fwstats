<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('email');
            $table->string('password');
            $table->string('remember_token', 100);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->unique('uuid');
            $table->unique('email');
        });
    }
};
