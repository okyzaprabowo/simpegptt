<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->default('')->comment('label / nama config nya');
            $table->string('group')->comment('group config nya');
            $table->string('key')->comment('id / key config nya');
            $table->text('value')->comment('isi / value config nya');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE config COMMENT = 'config umum'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config');
    }
}
