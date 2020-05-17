<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePegawaiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode',50)->default('');
            $table->string('nama',100)->default('');

            $table->unsignedBigInteger('instansi_id')->default(0);
            $table->unsignedInteger('jabatan_id')->default(0);
            $table->string('gelar_depan',10)->default('');
            $table->string('gelar_belakang',50)->default('');
            $table->string('ktp',50)->default('');
            $table->string('npwp',50)->default('');
            $table->date('tanggal_lahir')->nullable();
            $table->string('tempat_lahir',50)->default('');
            $table->tinyInteger('agama_id')->default(0);
            $table->tinyInteger('kelamin')->nullable()->comment('0: perempuan; 1: laki-laki');
            $table->string('golongan_darah',50)->default('');
            $table->tinyInteger('status_kawin_id')->default(0);
            $table->string('foto')->default('');
            
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
        Schema::dropIfExists('pegawai');
    }
}
