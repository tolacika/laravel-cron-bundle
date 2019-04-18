<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCronReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cron_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('job_id');
            $table->dateTime('run_at');
            $table->double('run_time');
            $table->integer('exit_code');
            $table->longText('output');
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
        Schema::dropIfExists('cron_reports');
    }
}
