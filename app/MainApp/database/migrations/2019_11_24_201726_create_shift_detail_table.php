<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShiftDetailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shift_detail', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->bigInteger('shift_id')->nullable();
            $table->tinyInteger('is_lintas_hari')->defautl(0)->comment('keterangan apakah tipe absen yang lewat hari');
			$table->boolean('tipe')->nullable()->comment('0 : default, 1:ramadhan');
			$table->time('senin_masuk')->nullable();
			$table->time('senin_pulang')->nullable();
			$table->time('selasa_masuk')->nullable();
			$table->time('selasa_pulang')->nullable();
			$table->time('rabu_masuk')->nullable();
			$table->time('rabu_pulang')->nullable();
			$table->time('kamis_masuk')->nullable();
			$table->time('kamis_pulang')->nullable();
			$table->time('jumat_masuk')->nullable();
			$table->time('jumat_pulang')->nullable();
			$table->time('sabtu_masuk')->nullable();
			$table->time('sabtu_pulang')->nullable();
			$table->time('minggu_masuk')->nullable();
			$table->time('minggu_pulang')->nullable();
			$table->date('range_awal')->nullable()->comment('awal periode diisi null jika tipe 0, diisi tanggal selain tipe 0');
			$table->date('range_akhir')->nullable()->comment('akhir periode diisi null jika tipe 0, diisi tanggal selain tipe 0');
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
		Schema::drop('shift_detail');
	}

}
