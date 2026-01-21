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
        Schema::create('kpi_evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // personal|divisi
            $table->string('kode'); // nik|id_divisi
            $table->date('periode');
            $table->foreignId('aspek_kpi_header_id')->constrained('aspek_kpi_headers')->cascadeOnDelete();
            $table->json('aspek_values')->nullable();
            $table->decimal('skor_akhir', 5, 2)->default(0.00);
            $table->unsignedInteger('evaluator_id')->nullable();
            $table->timestamps();

            $table->unique(
                ['kategori', 'kode', 'aspek_kpi_header_id', 'periode'],
                'kpi_evaluation_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_evaluations');
    }
};
