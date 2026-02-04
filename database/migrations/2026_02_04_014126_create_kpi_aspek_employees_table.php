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
        Schema::create('kpi_aspek_employees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kpi_employee_id')
                ->constrained('kpi_employees')
                ->cascadeOnDelete();

            $table->foreignId('aspek_kpi_header_id')
                ->nullable()
                ->constrained('aspek_kpi_headers')
                ->nullOnDelete();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_aspek_employees');
    }
};
