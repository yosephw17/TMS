<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('material_stock', function (Blueprint $table) {
            $table->string('reference_number')->nullable()->after('quantity');
            $table->decimal('unit_price', 10, 2)->nullable()->after('reference_number');
            $table->decimal('total_price', 12, 2)->nullable()->after('unit_price');
            $table->text('notes')->nullable()->after('total_price');
            
            // Add index for reference number
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::table('material_stock', function (Blueprint $table) {
            $table->dropIndex(['reference_number']);
            $table->dropColumn(['reference_number', 'unit_price', 'total_price', 'notes']);
        });
    }
};
