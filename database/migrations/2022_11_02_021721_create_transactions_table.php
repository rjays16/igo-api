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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->date('effective_date');
            $table->unsignedBigInteger('trans_type_id');
            $table->string('memo');
            $table->decimal('amount',20,6);
            $table->date('entry_date');
            $table->timestamps();
            $table->softDeletes();

            //Set Normal Indexes
            $table->index('account_id');
            $table->index('effective_date');
            $table->index('trans_type_id');
            $table->index('memo');
            $table->index('amount');
            $table->index('entry_date');
            $table->index('created_at');
            $table->index('deleted_at');

            //Set Foreign Key
            //To Clients table
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('restrict')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
