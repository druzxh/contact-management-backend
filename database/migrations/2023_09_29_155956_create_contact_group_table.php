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
        Schema::create('contact_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_code');
            $table->unsignedBigInteger('group_code');
            $table->timestamps();

            $table->foreign('contact_code')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('group_code')->references('id')->on('group_contacts')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_group');
    }
};