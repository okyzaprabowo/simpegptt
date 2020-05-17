<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAbsensiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn(['scan_time','type','pin']);
            $table->text('keterangan')->comment('keterangan tambahan')->after('id');
            $table->unsignedSmallInteger('status')->nullable()->comment('status absensi hari ini,null=alpha, 1=hadir, 2...')->after('id');
            $table->time('kekurangan_jam')->nullable()->comment('kekurangan jam masuk')->after('id');
            $table->time('total_jam')->nullable()->comment('jumlah jam masuk')->after('id');
            $table->time('jam_keluar')->nullable()->after('id');
            $table->time('jam_masuk')->nullable()->after('id');
            $table->date('tanggal')->nullable()->comment('tanggal absen')->after('id');
            $table->unsignedBigInteger('jenis_ijin_id')->nullable()->comment('null=alpha,0=masuk,ID IJIN')->after('id');
            $table->unsignedBigInteger('pegawai_id')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('absensi', function (Blueprint $table) {
            //
        });
    }
}
