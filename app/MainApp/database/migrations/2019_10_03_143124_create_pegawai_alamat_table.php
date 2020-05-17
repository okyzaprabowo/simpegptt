<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePegawaiAlamatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pegawai_alamat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pegawai_id');
            $table->tinyInteger('tipe_alamat')->default(1)->comment('1 : alamat sekarang
            2 : alamat ketika direkrut
            3 : alamat emergency');
            $table->text('alamat')->nullable();
            $table->string('kelurahan',100)->default('');
            $table->string('kecamatan',100)->default('');
            $table->string('kota',100)->default('');
            $table->string('provinsi',100)->default('');
            $table->string('kodepos',100)->default('');
            $table->string('telepon',100)->default('');
            $table->string('ponsel',100)->default('');
            $table->string('email',100)->default('');
            $table->string('emer_nama',100)->default('')->comment('khusus tipe alamat 3');
            $table->string('emer_pekerjaan',100)->default('')->comment('khusus tipe alamat 3');
            $table->string('emer_relasi',100)->default('')->comment('khusus tipe alamat 3');
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
        Schema::dropIfExists('pegawai_alamat');
    }
}
