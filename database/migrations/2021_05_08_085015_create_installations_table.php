<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installations', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->string('user_name');
            $table->string('purchase_from')->nullable();
            $table->string('purchase_code')->nullable();
            $table->string('mac')->nullable();
            $table->string('ip')->nullable();
            $table->integer('install_num');
            $table->string('verification_token');
            $table->dateTime('expired')->nullable();
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
        Schema::dropIfExists('installations');
    }
}
