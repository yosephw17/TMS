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
        // First, check if the table exists and what columns it has
        if (Schema::hasTable('purchase_request_material')) {
            Schema::table('purchase_request_material', function (Blueprint $table) {
                // Add cost tracking columns if they don't exist
                if (!Schema::hasColumn('purchase_request_material', 'total_cost')) {
                    $table->decimal('total_cost', 10, 2)->nullable()->after('quantity');
                }
                if (!Schema::hasColumn('purchase_request_material', 'weighted_avg_price')) {
                    $table->decimal('weighted_avg_price', 8, 2)->nullable()->after('total_cost');
                }
            });
        } else {
            // If table doesn't exist, create it with all necessary columns
            Schema::create('purchase_request_material', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_request_id')->constrained()->onDelete('cascade');
                $table->foreignId('material_id')->constrained()->onDelete('cascade');
                $table->integer('quantity');
                $table->decimal('total_cost', 10, 2)->nullable();
                $table->decimal('weighted_avg_price', 8, 2)->nullable();
                $table->timestamps();
                
                // Add indexes for better performance
                $table->index(['purchase_request_id', 'material_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('purchase_request_material')) {
            Schema::table('purchase_request_material', function (Blueprint $table) {
                if (Schema::hasColumn('purchase_request_material', 'total_cost')) {
                    $table->dropColumn('total_cost');
                }
                if (Schema::hasColumn('purchase_request_material', 'weighted_avg_price')) {
                    $table->dropColumn('weighted_avg_price');
                }
            });
        }
    }
};