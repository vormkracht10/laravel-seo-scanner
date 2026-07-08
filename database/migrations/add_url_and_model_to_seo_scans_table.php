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
        if (! Schema::hasTable('seo_scans')) {
            return;
        }

        Schema::table('seo_scans', function (Blueprint $table) {
            if (! Schema::hasColumn('seo_scans', 'url')) {
                $table->string('url')->nullable()->after('id');
            }

            if (! Schema::hasColumn('seo_scans', 'model_type')) {
                // String-based morph columns so subject models with integer,
                // uuid or ulid keys are all supported.
                $table->string('model_type')->nullable();
                $table->string('model_id')->nullable();
                $table->index(['model_type', 'model_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('seo_scans')) {
            return;
        }

        Schema::table('seo_scans', function (Blueprint $table) {
            if (Schema::hasColumn('seo_scans', 'model_type')) {
                $table->dropIndex(['model_type', 'model_id']);
                $table->dropColumn(['model_type', 'model_id']);
            }

            if (Schema::hasColumn('seo_scans', 'url')) {
                $table->dropColumn('url');
            }
        });
    }
};
