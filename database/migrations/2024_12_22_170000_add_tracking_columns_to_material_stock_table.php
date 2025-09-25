<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('material_stock', function (Blueprint $table) {
            // Original insertion data (never changes)
            $table->integer('original_quantity')->nullable()->after('quantity');
            $table->decimal('original_total_price', 12, 2)->nullable()->after('total_price');
            
            // Current remaining data (updates as materials are used)
            $table->integer('remaining_quantity')->nullable()->after('original_quantity');
            $table->decimal('current_total_value', 12, 2)->nullable()->after('original_total_price');
            
            // Movement tracking
            $table->integer('total_used')->default(0)->after('remaining_quantity');
            $table->decimal('total_used_value', 12, 2)->default(0)->after('current_total_value');
            
            // Status and tracking
            $table->enum('status', ['active', 'depleted', 'reserved'])->default('active')->after('notes');
            $table->timestamp('last_movement_at')->nullable()->after('status');
            $table->string('batch_number')->nullable()->after('reference_number');
            $table->date('expiry_date')->nullable()->after('batch_number');
            $table->string('supplier')->nullable()->after('expiry_date');
            $table->text('movement_log')->nullable()->after('supplier'); // JSON log of movements
            
            // Indexes for better performance
            $table->index(['status', 'remaining_quantity']);
            $table->index('batch_number');
        });
    }

    public function down(): void
    {
        Schema::table('material_stock', function (Blueprint $table) {
            $table->dropIndex(['status', 'remaining_quantity']);
            $table->dropIndex(['batch_number']);
            
            $table->dropColumn([
                'original_quantity', 'original_total_price', 'remaining_quantity', 
                'current_total_value', 'total_used', 'total_used_value', 
                'status', 'last_movement_at', 'batch_number', 'expiry_date', 
                'supplier', 'movement_log'
            ]);
        });
    }
};
