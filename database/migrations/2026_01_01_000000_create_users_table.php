<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 150)->unique();
            $table->string('password', 255);          // hash bcrypt
            $table->string('avatar', 255)->nullable(); // path file / URL
            $table->text('bio')->nullable();
            $table->rememberToken();
            $table->timestamps();                      // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
