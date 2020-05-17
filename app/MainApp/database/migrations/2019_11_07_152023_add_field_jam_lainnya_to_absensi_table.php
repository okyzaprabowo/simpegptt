<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldJamLainnyaToAbsensiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->time('kelebihan_jam')->nullable()->comment('kelebihan jam kerja')->after('kekurangan_jam');
            $table->time('keterlambatan_jam')->nullable()->comment('berapa lama keterlambatan')->after('kekurangan_jam');
            $table->time('pulang_cepat_jam')->nullable()->comment('berapa lama pulang kecepatannya')->after('kekurangan_jam');
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
            $table->dropColumn(['kelebihan_jam','keterlambatan_jam','pulang_cepat_jam']);
        });
    }
}
