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
        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrow_transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 8, 2);
            $table->enum('type', ['late_return', 'lost_book', 'damaged_book']);
            $table->enum('status', ['unpaid', 'paid', 'partially_paid', 'cancelled'])->default('unpaid');
            $table->date('issued_at');
            $table->timestamps();
            $table->timestamp('cancelled_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalties');
    }
};
