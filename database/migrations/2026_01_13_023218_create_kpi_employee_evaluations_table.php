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
        Schema::create('kpi_employee_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('aspek_kpi_header_id')->constrained('aspek_kpi_headers')->cascadeOnDelete();
            $table->unsignedInteger('bulan');
            $table->unsignedInteger('tahun');
            $table->decimal('realisasi', 5, 2)->default(0.00);
            $table->decimal('skor', 5, 2)->default(0.00);
            $table->timestamps();

            $table->unique(
                ['employee_id', 'aspek_kpi_header_id', 'bulan', 'tahun'],
                'kpi_employee_evaluation_period_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_employee_evaluations');
    }
};
