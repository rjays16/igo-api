<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompoundPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\CompoundPeriod::create(['compound_period'=>'Daily','description'=>'']);
        \App\Models\CompoundPeriod::create(['compound_period'=>'Weekly','description'=>'']);
        \App\Models\CompoundPeriod::create(['compound_period'=>'Monthly','description'=>'']);
        \App\Models\CompoundPeriod::create(['compound_period'=>'Quarterly','description'=>'']);
        \App\Models\CompoundPeriod::create(['compound_period'=>'Annually','description'=>'']);
        \App\Models\CompoundPeriod::create(['compound_period'=>'DailyEffAPR','description'=>'']);
    }
}
