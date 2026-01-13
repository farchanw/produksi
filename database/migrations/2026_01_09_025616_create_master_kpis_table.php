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
        Schema::create('master_kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_section_id')->constrained('master_sections')->cascadeOnDelete();
            $table->foreignId('master_subsection_id')->constrained('master_subsections')->cascadeOnDelete();
            $table->string('kategori');
            $table->string('area_kinerja_utama')->nullable();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('tipe');
            $table->string('satuan');
            $table->text('sumber_data_realisasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_kpis');
    }
};
