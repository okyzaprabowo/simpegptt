<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateJabatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jabatan', function (Blueprint $table) {
            $table->tinyInteger('is_waktu_shift')->default(0)->after('deskripsi');
            $table->string('instansi_ids',300)->nullable()->after('deskripsi')->comment('list id dari instansi mana saja (eselon II/ Unitkerja) jabatan tersebut berada. Separator koma');
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
            $table->dropColumn(['instansi_ids','is_waktu_shift']);
        });
    }
}
