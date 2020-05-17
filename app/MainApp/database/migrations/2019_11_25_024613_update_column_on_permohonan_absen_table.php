<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnOnPermohonanAbsenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permohonan_absen', function (Blueprint $table) {
            $table->date('waktu_selesai')->nullable()->change();
            $table->date('waktu_selesai')->nullable()->change();
            $table->text('template')->nullable()->comment('utk menampung template ijin, takutnya dikemudian hari master templatenya berubah')->after('approve_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permohonan_absen', function (Blueprint $table) {
            //
        });
    }
}
