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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('state',5);
            $table->string('city',150);
            $table->string('description',255);
            $table->timestamps();
            $table->softDeletes();

            //set Normal indexes
            $table->index('state');
            $table->index('city');
            $table->index('description');
            $table->index('created_at');
            $table->index('deleted_at');

            //Set Foreigh Keys
            $table->foreign('state')->references('state')->on('states')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
    }
};
