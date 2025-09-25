<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestockEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restock_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('purchase_request_id');
            $table->unsignedBigInteger('material_id');
            $table->unsignedBigInteger('stock_id');
            $table->unsignedBigInteger('restocked_by'); // user_id who performed restock
            
            // Restock details
            $table->decimal('quantity_restocked', 10, 3);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_cost_deducted', 12, 2); // Amount deducted from project cost
            $table->string('restock_reference')->unique(); // REF-PROJECT-MATERIAL-DATE-SEQ
            
            // Tracking information
            $table->text('reason')->nullable(); // Why was it restocked
            $table->text('notes')->nullable(); // Additional notes
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable(); // user_id who approved
            $table->timestamp('approved_at')->nullable();
            
            // Audit trail
            $table->json('original_purchase_data')->nullable(); // Store original purchase request data
            $table->json('stock_movement_log')->nullable(); // Track stock changes
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('cascade');
            $table->foreign('restocked_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes for better performance
            $table->index(['project_id', 'status']);
            $table->index(['purchase_request_id']);
            $table->index(['material_id', 'stock_id']);
            $table->index(['restocked_by']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restock_entries');
    }
}
