<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update2InstansiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instansi', function (Blueprint $table) {
            $table->string('nama',500)->default('')->comment('Nama Eselon IV dan unit eselonnya (child)')->change();
        });
        DB::statement("ALTER TABLE instansi MODIFY COLUMN singkatan VARCHAR(20) AFTER nama");
        DB::statement("ALTER TABLE instansi MODIFY COLUMN eselon TINYINT(1) DEFAULT 4 COMMENT 'tingkat eselon, 2 - 4' AFTER nama");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instansi', function (Blueprint $table) {
            //
        });
    }
}
