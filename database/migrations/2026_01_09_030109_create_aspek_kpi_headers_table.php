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
        Schema::create('aspek_kpi_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_section_id')->nullable()->constrained('master_sections')->nullOnDelete();
            $table->foreignId('master_subsection_id')->nullable()->constrained('master_subsections')->nullOnDelete();
            $table->unsignedInteger('tahun');
            $table->unsignedInteger('bulan');
            $table->timestamps();

            $table->unique(
                ['master_section_id', 'master_subsection_id', 'tahun', 'bulan'],
                'aspek_kpi_header_period_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspek_kpi_headers');
    }
};
