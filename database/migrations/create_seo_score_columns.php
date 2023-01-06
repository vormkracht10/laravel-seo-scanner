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
            $table->string('url');
            $table->nullableMorphs('model', 'model');
            $table->integer('score');
            $table->json('checks');
            $table->timestamps();
            $table->index('url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seo_scores');
    }
};
