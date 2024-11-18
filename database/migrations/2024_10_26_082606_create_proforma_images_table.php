<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProformaImagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proforma_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->onDelete('cascade'); // Foreign key for seller
            $table->foreignId('project_id')->constrained()->onDelete('cascade'); // Foreign key for project
            $table->string('image_path'); // Store the image file path
            $table->string('proforma_type'); // Type of proforma (e.g., aluminum, finishing)
            $table->string('description')->nullable(); 
            $table->enum('status', ['approved', 'declined', 'pending'])->default('pending'); // Status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proforma_images');
    }
}
