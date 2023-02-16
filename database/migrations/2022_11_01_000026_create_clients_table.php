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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name',100);
            $table->string('last_name',100);
            $table->string('gender',6);
            $table->date('date_of_birth');
            $table->string('email',100)->unique();
            $table->string('phone',50);
            $table->unsignedBigInteger('organization_id');
            $table->string('address1')->nullable();;
            $table->string('address2')->nullable();
            $table->unsignedBigInteger('city_id');
            $table->string('state',5);
            $table->string('zip',50);
            $table->unsignedBigInteger('client_type_id');
            $table->date('ca_date');
            $table->string('note')->nullable();
            $table->string('tag')->nullable();
            $table->timestamps();
            $table->softDeletes();

            //set Normal indexes
            $table->index('first_name');
            $table->index('last_name');
            $table->index('gender');
            $table->index('date_of_birth');
            //$table->index('email');
            $table->index('phone');
            $table->index('organization_id');
            $table->index('address1');
            $table->index('address2');
            $table->index('city_id');
            $table->index('state');
            $table->index('zip');
            $table->index('client_type_id');
            $table->index('ca_date');
            $table->index('note');
            $table->index('tag');
            $table->index('created_at');
            $table->index('deleted_at');


            //Set Foreign Keys
            //To Client Type Table
            $table->foreign('client_type_id')->references('id')->on('client_types')->onUpdate('restrict')->onDelete('restrict');
            //To Organization Table
            $table->foreign('organization_id')->references('id')->on('organizations')->onUpdate('restrict')->onDelete('restrict');
            //To State Table
            $table->foreign('state')->references('state')->on('states')->onUpdate('restrict')->onDelete('restrict');
            //To Cities Table
            $table->foreign('city_id')->references('id')->on('cities')->onUpdate('restrict')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
};
