<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProgramStudiPegawaiPendidikanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pegawai_pendidikan', function (Blueprint $table) {
            $table->string('program_studi')->default('')->after('tanggal_lulus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pegawai_pendidikan', function (Blueprint $table) {
            $table->dropColumn(['program_studi']);
        });
    }
}
