<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJamMasukAndKeluarColumnToJabatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jabatan', function (Blueprint $table) {
            $table->time('jam_pulang')->nullable()->after('deskripsi')->comment('Jam pulang untuk waktu normal');
            $table->time('jam_masuk')->nullable()->after('deskripsi')->comment('Jam masuk untuk waktu normal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jabatan', function (Blueprint $table) {
            $table->dropColumn(['jam_masuk','jam_pulang']);
        });
    }
}
