<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('cover_image')->nullable();
            $table->string('title');
            $table->string('isbn')->unique();
            $table->text('description')->nullable();
            $table->string('publisher')->nullable();
            $table->year('publication_year')->nullable();
            $table->enum('language', ['English', 'Filipino', 'Spanish', 'Chinese', 'Others'])->default('English');
            $table->float('price')->nullable();
            $table->foreignId('author_id')->constrained('authors')->onDelete('cascade');
            $table->foreignId('genre_id')->constrained('genres')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
