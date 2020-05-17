<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInstansiIdColumnInMesinAbsenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mesin_absen', function (Blueprint $table) {
            $table->unsignedInteger('instansi_id')->default(0)->after('ip');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mesin_absen', function (Blueprint $table) {
            $table->dropColumn(['instansi_id']);
        });
    }
}
