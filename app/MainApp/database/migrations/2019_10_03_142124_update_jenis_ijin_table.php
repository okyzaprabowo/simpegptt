<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateJenisIjinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jenis_ijin', function (Blueprint $table) {
            $table->tinyInteger('batas_ijin')->default(0)->after('deskripsi')->comment('batas ijin tertentu per bulannya');
            $table->text('template_keterangan')->nullable()->after('deskripsi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jenis_ijin', function (Blueprint $table) {
            $table->dropColumn(['batas_ijin','template_keterangan']);
        });
    }
}
