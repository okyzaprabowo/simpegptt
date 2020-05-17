<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsFormalColumnToPegawaiPendidikanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pegawai_pendidikan', function (Blueprint $table) {
            $table->tinyInteger('is_formal')->after('pegawai_id')->default(1);
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
            $table->dropColumn('is_formal');
        });
    }
}
