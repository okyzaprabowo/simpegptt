<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAbsensiIdColumnToAbsensiRawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absensi_raw', function (Blueprint $table) {
            $table->unsignedBigInteger('absensi_id')->default(0)->comment('id absensi yg sudah diproses, diisi setelah diproses')->after('absensi_raw_upload_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('absensi_raw', function (Blueprint $table) {
            $table->dropColumn(['absensi_id']);
        });
    }
}
