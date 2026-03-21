<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('productivities', function (Blueprint $table) {
            $table->id();
            $table->string('plant', 50)->default('Planta A');
            $table->string('product_line', 100);
            $table->unsignedInteger('produced_quantity');
            $table->unsignedInteger('defect_quantity');
            $table->date('production_date');
            $table->timestamps();

            $table->index(['plant', 'product_line']);
            $table->index('production_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('productivities');
    }
}
