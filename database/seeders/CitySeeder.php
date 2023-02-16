<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\City::create(['state'=>'KY','city'=>'Ashland','description'=>'']);
        \App\Models\City::create(['state'=>'AL','city'=>'Huntsville','description'=>'']);
        \App\Models\City::create(['state'=>'MA','city'=>'Boston','description'=>'']);
        \App\Models\City::create(['state'=>'AZ','city'=>'Phoenix','description'=>'']);
        \App\Models\City::create(['state'=>'CA','city'=>'Los Angeles','description'=>'']);
        \App\Models\City::create(['state'=>'NY','city'=>'Manhattan','description'=>'']);
        \App\Models\City::create(['state'=>'OK','city'=>'Tulsa','description'=>'']);
        \App\Models\City::create(['state'=>'OR','city'=>'Portland','description'=>'']);
        \App\Models\City::create(['state'=>'PA','city'=>'Philadelphia','description'=>'']);
        \App\Models\City::create(['state'=>'TX','city'=>'Dallas','description'=>'']);
    }
}
