<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsPeriodeColumnToJenisIjinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('jenis_ijin', 'is_periode'))
            Schema::table('jenis_ijin', function (Blueprint $table) {
                $table->tinyInteger('is_periode')->default(0)->after('id')->comment('1=>jenis ijin ini ada 2 inputan tanggal (dari - sampai), 0=>jenis ijin ini hanya memiliki 1 inputan tanggal');
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
            $table->dropColumn(['is_periode']);
        });
    }
}
