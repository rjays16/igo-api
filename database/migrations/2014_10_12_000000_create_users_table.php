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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name',100);
            $table->string('last_name',100);
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('address1',255);
            $table->string('address2',255);
            $table->unsignedBigInteger('city_id');
            $table->string('state',5);
            $table->string('zip',50);
            $table->string('phone',50);
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('client_id')->default(0);
            $table->string('picture',255)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            //set Normal indexes
            $table->index('first_name');
            $table->index('last_name');
            $table->index('phone');
            $table->index('address1');
            $table->index('address2');
            $table->index('city_id');
            $table->index('state');
            $table->index('zip');
            $table->index('role_id');
            $table->index('client_id');
            $table->index('created_at');
            $table->index('deleted_at');

             //Set Foreigh Key
            //To Roles Table
            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('restrict')->onDelete('restrict');
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
        Schema::dropIfExists('users');
    }
};
