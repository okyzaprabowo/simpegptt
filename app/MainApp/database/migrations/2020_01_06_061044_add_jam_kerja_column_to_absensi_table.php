<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AddJamKerjaColumnToAbsensiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->integer('jam_kerja')->default(0)->comment('jam kerja seharusnya')->after('scan_keluar');
        });

        $absensis = DB::table('absensi')->get();
        foreach ($absensis as $absensi) {
            $totalJamKerja = 0;
            if($absensi->jam_masuk && $absensi->status<=3){
                $jamKerjaMasuk = new Carbon($absensi->jam_masuk);
                $totalJamKerja = $jamKerjaMasuk->diffInSeconds($absensi->jam_keluar, false);
                DB::table('absensi')->where('id',$absensi->id)->update([
                    'jam_kerja' => $totalJamKerja
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn(['jam_kerja']);
        });
    }
}
