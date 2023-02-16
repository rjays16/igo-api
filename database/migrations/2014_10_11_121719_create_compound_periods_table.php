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
        Schema::create('compound_periods', function (Blueprint $table) {
            $table->id();
            $table->string('compound_period',50)->unique();
            $table->string('description');
            $table->timestamps();
            $table->softDeletes();

            //set Normal indexes
            //$table->index('compound_period');
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
        Schema::dropIfExists('compound_periods');
    }
};
