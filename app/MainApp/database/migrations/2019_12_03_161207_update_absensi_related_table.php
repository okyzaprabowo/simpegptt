<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateAbsensiRelatedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dateTime('jam_keluar_mulai_scan')->nullable()->comment('waktu awal scan akan dianggap sebagai jam keluar')->after('jam_masuk');
            $table->dateTime('jam_keluar_akhir_scan')->nullable()->comment('waktu akhir scan masih dianggap jam keluar')->after('jam_keluar');
            $table->dateTime('jam_masuk_mulai_scan')->nullable()->comment('waktu awal scan akan dianggap sebagai jam masuk')->after('tanggal');
            $table->dateTime('jam_masuk_akhir_scan')->nullable()->comment('waktu akhir scan masih dianggap jam masuk')->after('jam_masuk');

            $table->dateTime('jam_masuk')->nullable()->change();
            $table->dateTime('jam_keluar')->nullable()->change();
            $table->dateTime('scan_masuk')->nullable()->change();
            $table->dateTime('scan_keluar')->nullable()->change(); 

            $table->integer('total_jam')->default(0)->comment('jumlah jam kerja absen data ini dalam detik')->change();    
            $table->integer('kekurangan_jam')->default(0)->comment('jumlah kekurangan jam kerja absen data ini dalam detik')->change(); 
            $table->integer('pulang_cepat_jam')->default(0)->comment('jumlah berapa lama pulang cepatnya absen data ini dalam detik')->change(); 
            $table->integer('keterlambatan_jam')->default(0)->comment('jumlah keterlamtan jam kerja absen data ini dalam detik')->change();   
            $table->integer('kelebihan_jam')->default(0)->comment('jumlah kelebihan jam kerja absen data ini dalam detik')->change();            
            $table->dropColumn('keterangan');
        });
        DB::statement("ALTER TABLE `absensi` CHANGE `status` `status` SMALLINT(6) NULL DEFAULT '0' COMMENT '0:generate default; 1:hadir; 2:alpha; 3:telat/jam kerja kurang/scan tidak komplit; 4:ijin (ijinnya apa lihat di jenis_ijin_id); 5:libur hari libur atau shift ; 6:libur yang diset manual per pegawai';");
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
