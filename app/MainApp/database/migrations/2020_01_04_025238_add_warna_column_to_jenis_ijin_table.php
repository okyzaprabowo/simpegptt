<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddWarnaColumnToJenisIjinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jenis_ijin', function (Blueprint $table) {
            $table->string('warna')->default('#000000')->comment('warna label')->after('singkatan');
        });
        $jenisIjins = DB::table('jenis_ijin')->get();
        foreach ($jenisIjins as $jenisIjin) {
            DB::table('jenis_ijin')->where('id',$jenisIjin->id)->update([
                'warna' => '#'.dechex( mt_rand( 0, 255 ) ).dechex( mt_rand( 0, 255 ) ).dechex( mt_rand( 0, 255 ) )
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jenis_ijin', function (Blueprint $table) {
            $table->dropColumn(['warna']);
        });
    }
}
