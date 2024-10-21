<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id'); // Foreign key for the project
            $table->string('file_name'); // Name of the file
            $table->string('file_path'); // Path to the file or image
            $table->string('file_type')->nullable(); // Type of file (e.g., image, pdf, etc.)
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_files');
    }
}
