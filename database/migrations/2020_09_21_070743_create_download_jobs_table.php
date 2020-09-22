<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDownloadJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('download_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('url')->index();
            $table->string('name');
            $table->double('size');
            $table->string('sha1sum');
            $table->string('csv_gz_path')->nullable();
            $table->tinyInteger('version');
            $table->enum('sync_stage',['pending','fetched_csv_info','downloaded_started', 'downloaded_complete','unzipped','stored_to_temp_table','completed'])->default('pending')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('download_jobs');
    }
}
