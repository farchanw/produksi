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
        Schema::create('inventory_consumables', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->unsignedBigInteger('category_id')->nullable();

            $table->foreign('category_id')
                ->references('id')
                ->on('inventory_consumable_categories')
                ->onDelete('set null');

            $table->integer('minimum_stock')->default(0);
            $table->string('satuan');
            $table->integer('harga_satuan')->default(0);
            $table->timestamps();
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_consumables');
    }
};
