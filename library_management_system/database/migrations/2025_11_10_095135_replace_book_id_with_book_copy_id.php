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
        Schema::table('borrow_transactions', function (Blueprint $table) {
            // Drop the old book_id foreign key and column
            $table->dropForeign(['book_id']);
            $table->dropColumn('book_id');

            $table->foreignId('book_copy_id')->nullable()->constrained('book_copies')->nullOnDelete()->after('user_id'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
     Schema::table('borrow_transactions', function (Blueprint $table) {
            // Drop the old book_id foreign key and column
            $table->dropForeign(['book_id']);
            $table->dropColumn('book_id');

            $table->foreignId('book_copy_id')->nullable()->constrained('book_copies')->nullOnDelete()->after('user_id'); 
        });
    }
};
