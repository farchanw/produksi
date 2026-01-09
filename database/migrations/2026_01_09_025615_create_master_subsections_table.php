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
        Schema::create('master_subsections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_section_id')
                ->nullable()
                ->constrained('master_sections')
                ->nullOnDelete();

            $table->string('nama');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_subsections');
    }
};
