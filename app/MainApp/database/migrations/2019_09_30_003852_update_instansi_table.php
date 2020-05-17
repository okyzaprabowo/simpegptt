<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInstansiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instansi', function (Blueprint $table) {
            $table->string('singkatan')->comment('kode instansi');
            $table->tinyInteger('eselon')->default('4')->comment('tingkat eselon, 2 - 4');
            $table->string('induk_path')->default('')->comment('seluruh parent id, format : ;parent_parent_id;parent_id;')->after('induk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn(['singkatan','eselon','induk_path']);
        });
    }
}
