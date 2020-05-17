<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbsensiRawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absensi_raw', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mesin_absensi_id')->default(0);
            $table->unsignedBigInteger('pegawai_id')->default(0)->comment('auto detek saat get data absen');
            $table->string('pin',50)->default('');
            $table->dateTime('scan_time')->nullable();
            $table->string('device_id',10)->default('')->comment('Device id dari mesin');
            $table->string('type',10)->default('')->comment('Jenis absensi dari mesin, 0=in, 1=out, 2=break in, 3=break out,4=overtime in, 5=overtime out');
            $table->string('data_type',10)->default('')->comment('Jenis data dari mesin, 0=absensi dgn password, 1=absensi dgn sidik jari');
            $table->string('work_code')->default('');
            $table->tinyInteger('status')->default(0)->comment('0 data baru, 1 data sudah diproses');
            $table->tinyInteger('is_manual')->default(0)->comment('0 data auto get dari mesin, 1 data upload manual');
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
        Schema::dropIfExists('absensi_raw');
    }
}
