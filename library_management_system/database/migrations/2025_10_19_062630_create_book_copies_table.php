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
        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('copy_number');
            $table->enum('status', ['available', 'borrowed', 'withdrawn', 'lost', 'damaged', 'pending_issue_review', 'on_hold_for_pickup'])->default('available');
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
            $table->unique(['book_id', 'copy_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};
