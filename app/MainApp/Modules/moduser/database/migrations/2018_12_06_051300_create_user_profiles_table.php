<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            
            $table->string('avatar')->default('');

            $table->boolean('gender')->default(0);
            
            $table->date('date_of_birth')->nullable();
            
            $table->string('socnet_facebook')->default('');
            $table->string('socnet_instagram')->default('');
            $table->string('address')->default('');
                        
            $table->string('postal_code')->default('');
                        
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
        Schema::dropIfExists('user_profiles');
    }
}
