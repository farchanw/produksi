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
        Schema::create('inventory_consumable_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inventory_consumables')->cascadeOnDelete();
            //$table->foreignId('subcategory_id')->nullable()->constrained('inventory_consumable_subcategories')->nullOnDelete();
            $table->integer('qty');
            $table->string('type');
            $table->integer('stock_awal');
            $table->integer('stock_akhir');
            $table->bigInteger('harga_satuan')->default(0);
            $table->bigInteger('harga_total')->default(0);
            $table->datetime('movement_datetime');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_consumable_movements');
    }
};
