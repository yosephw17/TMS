<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseRequestsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('stock_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['material_stock', 'material_non_stock', 'labour', 'transport']); 
            $table->string('non_stock_name')->nullable(); 
            $table->decimal('non_stock_price', 10, 2)->nullable(); 
            $table->integer('non_stock_quantity')->nullable(); 
            $table->string('non_stock_image')->nullable(); 
            $table->text('details')->nullable(); 
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); 
            $table->timestamps();
        });

        // Pivot table for the materials requested from stock
        Schema::create('purchase_request_material', function (Blueprint $table) {
            $table->foreignId('purchase_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_request_material');
        Schema::dropIfExists('purchase_requests');
    }
}
