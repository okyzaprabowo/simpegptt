<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddInstansiIndukPathToPegawaiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('instansi_induk_path')->default('')->comment('seluruh parent id instansi pegawai berada, format : ;parent_parent_id;parent_id;')->after('instansi_id');
        });

        $profiles = DB::table('pegawai')->where('instansi_id','!=',0)->get();
        $instansi = [];
        foreach ($profiles as $v) {
            if(!isset($instansi[$v->instansi_id])){
                $instansi[$v->instansi_id] = DB::table('instansi')->where('id',$v->instansi_id)->first();
            }           
            DB::table('pegawai')->where('id',$v->id)->update([
                'instansi_induk_path' => $instansi[$v->instansi_id]->induk_path
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
        Schema::table('pegawai', function (Blueprint $table) {
            //
        });
    }
}
