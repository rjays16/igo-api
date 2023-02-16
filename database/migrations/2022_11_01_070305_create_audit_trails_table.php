<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('pages');
            $table->string('activity');
            $table->timestamps();
            $table->softDeletes();

            //set Normal indexes
            $table->index('user_id');
            $table->index('pages');
            $table->index('activity');
            $table->index('created_at');
            $table->index('deleted_at');

            //Set Foreigh Key
            //To Users Table
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('restrict')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_trails');
    }
};
