<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostTrackingToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            // Cost tracking fields
            $table->decimal('actual_cost', 12, 2)->default(0)->after('total_price');
            $table->decimal('material_cost', 12, 2)->default(0)->after('actual_cost');
            $table->decimal('labour_cost', 12, 2)->default(0)->after('material_cost');
            $table->decimal('transport_cost', 12, 2)->default(0)->after('labour_cost');
            $table->decimal('other_cost', 12, 2)->default(0)->after('transport_cost');
            
            // Cost tracking metadata
            $table->timestamp('cost_last_updated')->nullable()->after('other_cost');
            $table->json('cost_breakdown')->nullable()->after('cost_last_updated');
            
            // Budget tracking
            $table->decimal('budget_variance', 12, 2)->default(0)->after('cost_breakdown');
            $table->decimal('cost_percentage', 5, 2)->default(0)->after('budget_variance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'actual_cost',
                'material_cost', 
                'labour_cost',
                'transport_cost',
                'other_cost',
                'cost_last_updated',
                'cost_breakdown',
                'budget_variance',
                'cost_percentage'
            ]);
        });
    }
}
