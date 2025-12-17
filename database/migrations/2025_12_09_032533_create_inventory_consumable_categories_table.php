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
            $table->engine('InnoDB');

            $table->id();
            $table->string('name');

            $table->unsignedBigInteger('parent_id')->nullable();

            $table->timestamps();
        });

        Schema::table('inventory_consumable_categories', function (Blueprint $table) {
            $table->foreign('parent_id')
                ->references('id')
                ->on('inventory_consumable_categories')
                ->nullOnDelete();
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
