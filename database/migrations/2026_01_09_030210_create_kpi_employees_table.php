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
        Schema::create('kpi_employees', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->string('nik')->unique();

            $table->foreignId('master_section_id')
                ->nullable()
                ->constrained('master_sections')
                ->nullOnDelete();

            $table->foreignId('master_subsection_id')
                ->nullable()
                ->constrained('master_subsections')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_employees');
    }
};
