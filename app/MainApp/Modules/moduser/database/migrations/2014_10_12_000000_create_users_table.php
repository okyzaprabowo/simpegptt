<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('all_tenant')->default(1);
            
            $table->string('user_idcode')->default('');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->default('');
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password')->default('');
            $table->string('auth_password')->default('');
            $table->text('note')->nullable();
            $table->text('role')->nullable();
            $table->tinyInteger('level')->nullable(2);            
            
            $table->string('socialauth_facebook_id')->default('');
            $table->string('socialauth_facebook_token')->default('');
            $table->text('socialauth_facebook_data')->nullable();
            
            $table->string('socialauth_google_id')->default('');
            $table->string('socialauth_google_token')->default('');
            $table->text('socialauth_google_data')->nullable();
            
            $table->tinyInteger('status')->default(0);
            $table->string('banned_note')->default('');
            $table->timestamp('banned_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
