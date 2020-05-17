<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTenantGroupConcept extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->tinyInteger('tenant_group_id')->default(0)->after('id')->comment('0: all tenant, else id main tenant group');
        });
        Schema::table('tenants', function (Blueprint $table) {
            $table->tinyInteger('tenant_group_id')->default(0)->after('protected')->comment('0: all tenant, else id main tenant group');
        });
        Schema::table('tenant_groups', function (Blueprint $table) {
            $table->tinyInteger('is_main')->default(0)->after('protected')->comment('1: main group');
            $table->unsignedInteger('default_role_id')->default(0)->comment('default id role saat tenant pertama kali di-create');
            $table->string('default_role_code')->default('')->comment('default role code (sama seperti role id) saat tenant pertama kali di-create');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('tenant_group_id');
        });
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('tenant_group_id');
        });
        Schema::table('tenant_groups', function (Blueprint $table) {
            $table->dropColumn('is_main');
        });
    }
}
