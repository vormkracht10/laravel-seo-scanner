<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Widen the morph id column from an unsigned big integer to a string so
     * subject models with integer, uuid or ulid keys are all supported.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('seo_scores') || $this->modelIdIsString()) {
            return;
        }

        Schema::table('seo_scores', function (Blueprint $table) {
            $table->string('model_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('seo_scores') || ! $this->modelIdIsString()) {
            return;
        }

        Schema::table('seo_scores', function (Blueprint $table) {
            $table->unsignedBigInteger('model_id')->nullable()->change();
        });
    }

    private function modelIdIsString(): bool
    {
        return in_array(Schema::getColumnType('seo_scores', 'model_id'), ['varchar', 'character varying', 'string', 'text']);
    }
};
