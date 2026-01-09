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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->foreignId('master_section_id')->constrained('master_sections')->cascadeOnDelete();
            $table->foreignId('master_subsection_id')->constrained('master_subsections')->cascadeOnDelete();
            $table->unsignedBigInteger('aspek_kpi_header_id')->nullable();
            $table->timestamps();

            $table->foreign('aspek_kpi_header_id')
                ->references('id')
                ->on('aspek_kpi_headers')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
