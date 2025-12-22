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
        Schema::create('inventory_consumable_movement_subcategory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movement_id')->constrained('inventory_consumable_movements')->cascadeOnDelete();
            $table->foreignId('subcategory_id')->constrained('inventory_consumable_subcategories')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_consumable_movement_subcategory');
    }
};
