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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role',150)->unique();
            $table->string('description',255);
            $table->text('permission');
            $table->timestamps();
            $table->softDeletes();

            //set Normal indexes
            //$table->index('role');
            $table->index('description');
            $table->index('created_at');
            $table->index('deleted_at');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
};
