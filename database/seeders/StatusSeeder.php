<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;


use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        \App\Models\Status::create(['status'=>'INACTIVE','description'=>'Inactive Status']);
        \App\Models\Status::create(['status'=>'ACTIVE','description'=>'Active Status']);
        \App\Models\Status::create(['status'=>'PENDING','description'=>'Pending Status']);
        \App\Models\Status::create(['status'=>'ON PROGRESS','description'=>'On Progress Status']);

    }
}
