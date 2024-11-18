<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('proforma_material', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proforma_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('quantity', 10, 2); // Quantity of material
            $table->decimal('total_price', 15, 2); // Total price (quantity * unit price)
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('proforma_id')->references('id')->on('proformas')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
        });
    }

    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proforma_material');
    }
};
