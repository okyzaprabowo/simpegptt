<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreatePermohonanAbsenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('permohonan_absen'))
            Schema::create('permohonan_absen', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->date('tanggal')->nullable();
                $table->bigInteger('pegawai_id')->nullable();
                $table->integer('ijin_id')->nullable();
                $table->date('waktu_mulai')->nullable();
                $table->date('waktu_selesai')->nullable();
                $table->text('keterangan', 65535)->nullable();
                $table->string('approve_by', 100)->nullable();
                $table->dateTime('approve_at')->default('1000-01-01 00:00:00');
                $table->text('approve_desc', 65535)->nullable();
                $table->tinyInteger('approve_status')->default(0)->nullable();
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permohonan_absen');
    }
}
