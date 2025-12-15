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
        Schema::create('inventory_consumable_categories', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->foreign('parent_id')
                ->references('id')
                ->on('inventory_consumable_categories')
                ->nullOnDelete();
            $table->unsignedBigInteger('parent_key')
                ->storedAs('IFNULL(parent_id, 0)');
            $table->unique(['name', 'parent_key']);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_consumable_categories');
    }
};
