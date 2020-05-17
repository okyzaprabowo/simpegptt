<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusOldColumnToAbsensiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('absensi','status_old'))
            Schema::table('absensi', function (Blueprint $table) {
                $table->unsignedTinyInteger('status_old')->default(0)->comment('buat nampung status sebelum diubah oleh approval permohonan')->after('status');
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
            $table->dropColumn(['status_old']);
        });
    }
}
