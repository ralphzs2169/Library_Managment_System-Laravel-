<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // First, update the enum to include 'suspended'
            $table->enum('library_status', ['active', 'cleared', 'with_penalty', 'suspended'])
                  ->default('active')
                  ->change();
        });

        // Now, safely update rows
        DB::table('users')
            ->where('library_status', 'with_penalty')
            ->update(['library_status' => 'suspended']);

        // Optionally, remove 'with_penalty' from enum if you want
        Schema::table('users', function (Blueprint $table) {
            $table->enum('library_status', ['active', 'cleared', 'suspended'])
                  ->default('active')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add 'with_penalty' back
        Schema::table('users', function (Blueprint $table) {
            $table->enum('library_status', ['active', 'cleared', 'suspended', 'with_penalty'])
                  ->default('active')
                  ->change();
        });

        // Revert updated rows
        DB::table('users')
            ->where('library_status', 'suspended')
            ->update(['library_status' => 'with_penalty']);

        // Optionally, remove 'suspended' from enum
        Schema::table('users', function (Blueprint $table) {
            $table->enum('library_status', ['active', 'cleared', 'with_penalty'])
                  ->default('active')
                  ->change();
        });
    }
};
