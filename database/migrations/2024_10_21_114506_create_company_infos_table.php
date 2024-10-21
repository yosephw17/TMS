<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_infos', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('name'); // Company name
            $table->string('phone'); // Phone number
            $table->string('fax')->nullable(); // Fax number
            $table->string('logo')->nullable(); // Logo path or URL
            $table->string('po_box')->nullable(); // PO Box
            $table->string('email')->unique(); // Unique email address
            $table->string('motto')->nullable(); // Company motto
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_infos'); // Corrected to match the table name
    }
};
