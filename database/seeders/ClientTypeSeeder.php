<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\ClientType::create(['client_type'=>'OWN','description'=>'Owners']);
        \App\Models\ClientType::create(['client_type'=>'CHD','description'=>'Children']);
        \App\Models\ClientType::create(['client_type'=>'EXF','description'=>'Extended Family']);
        \App\Models\ClientType::create(['client_type'=>'FRN','description'=>'Friends']);
        \App\Models\ClientType::create(['client_type'=>'ORG','description'=>'Organizations']);
        \App\Models\ClientType::create(['client_type'=>'TBD','description'=>'To be determine']);
    }
}
