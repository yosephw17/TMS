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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Type of notification (project_created, customer_added, etc.)
            $table->string('title'); // Notification title
            $table->text('message'); // Notification message
            $table->json('data')->nullable(); // Additional data (IDs, links, etc.)
            $table->unsignedBigInteger('user_id'); // User who should receive the notification
            $table->unsignedBigInteger('created_by')->nullable(); // User who triggered the notification
            $table->string('icon')->default('fas fa-bell'); // Icon for the notification
            $table->string('color')->default('primary'); // Color theme (primary, success, warning, danger, info)
            $table->string('action_url')->nullable(); // URL to redirect when clicked
            $table->boolean('is_read')->default(false); // Read status
            $table->timestamp('read_at')->nullable(); // When it was read
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['user_id', 'is_read']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
