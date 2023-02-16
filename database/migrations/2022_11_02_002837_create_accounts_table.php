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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            //$table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('status_id')->default(1);
            $table->unsignedBigInteger('creditor_id');
            $table->string('acct_description');
            $table->integer('acct_number');
            $table->unsignedBigInteger('debtor_id');
            $table->unsignedBigInteger('term_id');
            $table->decimal('current_rate',15,2);           //Denormalized Column
            $table->string('note');
            $table->date('origin_date');
            $table->string('tag');
            $table->timestamps();
            $table->softDeletes();

            //Set Normal Indexes
           // $table->index('client_id');
            $table->index('status_id');
            $table->index('creditor_id');
            $table->index('acct_description');
            $table->index('acct_number');
            $table->index('debtor_id');
            $table->index('term_id');
            $table->index('current_rate');                              //Denormalized Column
            $table->index('note');
            $table->index('origin_date');
            $table->index('tag');
            $table->index('created_at');
            $table->index('deleted_at');

            //Set Foreigh Key

            //To Clients table
           // $table->foreign('client_id')->references('id')->on('clients')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('creditor_id')->references('id')->on('clients')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('debtor_id')->references('id')->on('clients')->onUpdate('restrict')->onDelete('restrict');

            //To Terms Table
            $table->foreign('term_id')->references('id')->on('terms')->onUpdate('restrict')->onDelete('restrict');

            //To Statuses Table
            $table->foreign('status_id')->references('id')->on('statuses')->onUpdate('restrict')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
