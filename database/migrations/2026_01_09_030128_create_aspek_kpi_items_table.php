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
        Schema::create('aspek_kpi_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aspek_kpi_header_id');
            $table->unsignedBigInteger('master_kpi_id');
            $table->decimal('score', 5, 2)->nullable();
            $table->decimal('bobot', 5, 2)->nullable();
            $table->decimal('target', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(
                ['aspek_kpi_header_id', 'master_kpi_id'],
                'aspek_kpi_header_master_kpi_idx'
            );

            $table->foreign('aspek_kpi_header_id')
                ->references('id')
                ->on('aspek_kpi_headers')
                ->onDelete('cascade');

            $table->foreign('master_kpi_id')
                ->references('id')
                ->on('master_kpis')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspek_kpi_items');
    }
};
