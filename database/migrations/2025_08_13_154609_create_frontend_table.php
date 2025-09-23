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
        Schema::create('frontends', function (Blueprint $table) {
            $table->id();
            $table->string('project_name'); // Name to display on frontend
            $table->text('description')->nullable();
            $table->date('date')->nullable(); // Project date
            $table->string('client')->nullable(); // Client name
            $table->string('image')->nullable(); // Path to project image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frontends');
    }
};
