<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('isActive')->default(1);
            $table->tinyInteger('isDelete')->default(0);
            $table->string('users_code');
            $table->string('username')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('isActive');
            $table->dropColumn('isDelete');
            $table->dropColumn('users_code');
            $table->dropColumn('username');
        });
    }
};