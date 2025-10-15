<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProformaWorkTable extends Migration
{
    public function up()
    {
        Schema::create('proforma_works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proforma_id')->constrained()->onDelete('cascade');
            $table->string('work_name');
            $table->string('work_unit');
            $table->decimal('work_amount', 10, 2)->nullable();
            $table->integer('work_quantity');
            $table->decimal('work_total', 10, 2);

            // User tracking fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proforma_work');
    }
}
