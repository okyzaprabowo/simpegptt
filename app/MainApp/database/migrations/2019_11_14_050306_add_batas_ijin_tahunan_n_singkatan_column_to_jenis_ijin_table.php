<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBatasIjinTahunanNSingkatanColumnToJenisIjinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        
        Schema::table('jenis_ijin', function (Blueprint $table) {
            if(!Schema::hasColumn('jenis_ijin', 'batas_ijin_tahunan'))
                $table->smallInteger('batas_ijin_tahunan')->default(0)->comment('batas ijin tertentu per tahunnya contoh cuti')->after('batas_ijin');
            if(!Schema::hasColumn('jenis_ijin', 'singkatan'))
                $table->string('singkatan')->default('')->comment('utk kepentingan column header di rekap absensi')->after('deskripsi');
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
            $table->dropColumn(['batas_ijin_tahunan','singkatan']);
        });
    }
}
