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
        Schema::create('kpi_personal_oees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_employees_id')->constrained('kpi_employees')->cascadeOnDelete();
            $table->date('periode');
            $table->float('availability', 5, 2)->default(0.00);
            $table->float('performance', 5, 2)->default(0.00);
            $table->float('quality', 5, 2)->default(0.00);
            $table->float('oee', 5, 2)->default(0.00);
            $table->integer('target')->default(0);
            $table->integer('bobot')->default(0);
            $table->float('nilai')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_personal_oees');
    }
};
