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
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->date('effective_date');
            $table->decimal('rate',15,2);
            $table->unsignedBigInteger('compound_period_id');
            $table->string('note');
            $table->timestamps();
            $table->softDeletes();

            //set Normal indexes
            $table->index('account_id');
            $table->index('effective_date');
            $table->index('rate');
            $table->index('compound_period_id');
            $table->index('note');
            $table->index('created_at');
            $table->index('deleted_at');

            //Set Foreigh Key
            //To Compound_periods table
            $table->foreign('compound_period_id')->references('id')->on('compound_periods')->onUpdate('restrict')->onDelete('restrict');

            //To Accounts table
           // $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('terms');
    }
};
