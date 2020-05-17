<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJenisIjinKategoriTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jenis_ijin_kategori', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama')->default('');
            $table->string('singkatan')->default('')->comment('untuk keperluan judul kolom');
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
        Schema::dropIfExists('jenis_ijin_kategori');
    }
}
