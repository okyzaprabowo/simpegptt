<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('session_id',191)->nullable();
            $table->unsignedInteger('user_id')->default(0);
            $table->tinyInteger('is_mobileapps_token')->default(0);
            $table->string('device_id')->default('');
            $table->string('api_token', 150)->unique();
            $table->string('push_token')->default('');//push notif token
            $table->tinyInteger('push_type')->default(0);//0 tidak ada push, 1 firebase, 2
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
        Schema::dropIfExists('api_tokens');
    }
}
