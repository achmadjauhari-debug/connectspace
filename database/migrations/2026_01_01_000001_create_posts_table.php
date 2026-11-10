<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('content')->nullable();       // opsional jika ada gambar
            $table->string('image', 255)->nullable();  // path file / URL
            $table->timestamps();                      // created_at, updated_at
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
