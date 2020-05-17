<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateApproveStatusColumnOnPermohonanAbsenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permohonan_absen', function (Blueprint $table) {
            $table->dateTime('waktu_selesai')->nullable()->change();
            $table->dateTime('approve_at')->nullable()->change();
            $table->dateTime('updated_at')->nullable()->change();
            $table->dateTime('created_at')->nullable()->change();
        });

        DB::statement("ALTER TABLE `permohonan_absen` CHANGE `approve_status` `approve_status` SMALLINT(6) DEFAULT 0 NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('permohonan_absen', function (Blueprint $table) {
            
        // });
    }
}
