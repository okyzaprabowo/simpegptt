<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbsensiRawUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absensi_raw_uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama')->comment('nama file jika upload manual');
            $table->unsignedBigInteger('mesin_absensi_id')->default(0);
            $table->string('file')->comment('filename raw file nya');
            $table->tinyInteger('status')->default(0)->comment('status data rawnya sudah diinsert ke table absensi_raw atau belum, 0=new file, 1=masih proses insert, 2=sudah diinsert semua');
            $table->tinyInteger('is_from_file')->default(0)->comment('0=auto langsung dari mesin, 1=upload via file raw');
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
        Schema::dropIfExists('absensi_raw_uploads');
    }
}
