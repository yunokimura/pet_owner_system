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
        // Create the master traits table
        Schema::create('traits', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., Friendly, Playful, Calm
            $table->timestamps();
        });

        // Create the pivot table pet_traits
        Schema::create('pet_traits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adoption_id')
                ->constrained('adoption_pets')
                ->onDelete('cascade'); // Cascade delete when pet is deleted
            $table->foreignId('trait_id')
                ->constrained('traits')
                ->onDelete('cascade'); // Cascade delete when trait is deleted
            $table->timestamps();
            
            // Ensure unique combination of adoption_id and trait_id
            $table->unique(['adoption_id', 'trait_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_traits');
        Schema::dropIfExists('traits');
    }
};