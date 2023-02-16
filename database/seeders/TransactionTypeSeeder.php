<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\TransactionType::create(['trans_type'=>'DEP','description'=>'Deposit']);
        \App\Models\TransactionType::create(['trans_type'=>'CRD','description'=>'Credit (Internal Funds)']);
        \App\Models\TransactionType::create(['trans_type'=>'TRN','description'=>'Transfer (Loan to Lon)']);
        \App\Models\TransactionType::create(['trans_type'=>'WTD','description'=>'Withdrawal']);
        \App\Models\TransactionType::create(['trans_type'=>'IEP','description'=>'Interest Earned Periodic']);
        \App\Models\TransactionType::create(['trans_type'=>'INA','description'=>'Interest Adjustments']);
        \App\Models\TransactionType::create(['trans_type'=>'ADJ','description'=>'Adjustment']);
        \App\Models\TransactionType::create(['trans_type'=>'MEM','description'=>'Memo or Account Annotation']);
    }
}
