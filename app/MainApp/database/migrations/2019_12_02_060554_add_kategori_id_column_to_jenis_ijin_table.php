<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKategoriIdColumnToJenisIjinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jenis_ijin', function (Blueprint $table) {
            $table->unsignedBigInteger('jenis_ijin_kategori_id')->default(0)->after('is_periode');
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
            $table->dropColumn(['jenis_ijin_kategori_id']);
        });
    }
}
