<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsShowScannerColumnToJenisIjinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jenis_ijin', function (Blueprint $table) {
            $table->tinyInteger('is_show_scanner')->default(0)->comment('0:kosongkan 1:isi --> field scanner masuk dan keluar walaupun ada permohonan ijin')->after('id');
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
            $table->dropColumn(['is_show_scanner']);
        });
    }
}
