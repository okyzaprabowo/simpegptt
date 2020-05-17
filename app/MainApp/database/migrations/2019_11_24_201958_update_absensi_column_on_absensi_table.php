<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAbsensiColumnOnAbsensiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('absensi', function (Blueprint $table) {
            $table->tinyInteger('is_lintas_hari')->defautl(0)->comment('keterangan apakah tipe absen yang lewat hari')->after('jenis_ijin_id');
            $table->unsignedBigInteger('shift_id')->defautl(0)->comment('tipe shift saat scan diproses')->after('jenis_ijin_id');

            $table->unsignedBigInteger('permohonan_id')->default(0)->comment('link ke table permohonan_absen diisi ketika approval permohonan_absen')->after('keterangan');
            $table->time('scan_keluar')->nullable()->comment('waktu keluar dari raw absen')->after('jam_keluar');
            $table->time('scan_masuk')->nullable()->comment('waktu masuk dari raw absen')->after('jam_keluar');
            
            $table->time('jam_masuk')->nullable()->comment('ambil dari waktu masuk shift sesuaikan hari nya dengan kolom hari di table shift_detail')->change();
            $table->time('jam_keluar')->nullable()->comment('ambil dari waktu pulang shift sesuaikan hari nya dengan kolom hari di table shift_detail')->change();

            $table->smallInteger('status')->default(0)->comment('0:generate default; 1:hadir; 2:alpha; 3:telat; 4:ijin (ijinnya apa lihat di jenis_ijin_id); 5:libur')->change();
            $table->bigInteger('jenis_ijin_id')->default(0)->comment('0:efault; selain itu terserah diisi apa dan diupdate pd saat approval permohonan_absen')->change();
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
