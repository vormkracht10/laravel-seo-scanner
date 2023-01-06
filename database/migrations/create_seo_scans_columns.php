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
        Schema::create('seo_scans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('pages')->nullable();
            $table->double('time', 10, 5)->nullable();
            $table->timestamps();
            $table->double('started_at', 16, 6)->nullable();
            $table->double('ended_at', 16, 6)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seo_scans');
    }
};
