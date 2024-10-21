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
        Schema::create('proformas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('ref_no')->unique()->nullable();
            $table->decimal('before_vat_total', 15, 2);
            $table->integer('vat_percentage')->default(15); // Default VAT 15%
            $table->decimal('vat_amount', 15, 2);
            $table->decimal('after_vat_total', 15, 2);
            $table->decimal('discount', 15, 2)->nullable();
            $table->decimal('final_total', 15, 2);
            $table->string('payment_validity')->nullable(); 
            $table->string('delivery_terms')->nullable(); 

            $table->enum('type', ['aluminium_profile', 'aluminium_accessories', 'work']);
            $table->date('date');
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proformas');
    }
};
