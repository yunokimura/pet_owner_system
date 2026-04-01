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
        // First, drop the trait_1 and trait_2 columns from adoption_pets
        Schema::table('adoption_pets', function (Blueprint $table) {
            $table->dropColumn(['trait_1', 'trait_2']);
        });

        // Create the pet_traits table
        Schema::create('pet_traits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adoption_pet_id')->constrained('adoption_pets')->onDelete('cascade');
            $table->string('name'); // e.g., "Loyal", "Intelligent"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_traits');
        
        // Add back the columns if rolled back
        Schema::table('adoption_pets', function (Blueprint $table) {
            $table->string('trait_1')->nullable();
            $table->string('trait_2')->nullable();
        });
    }
};