<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        //Execute Seeds for Role first - Super Admin and Client
        \App\Models\Role::create(['role'=>'Super Admin','description'=>'Super Admin has all access to all parts of the system.','permission'=>'']);
        \App\Models\Role::create(['role'=>'Client Access','description'=>'Client Role allows client to access only limited parts of the system.','permission'=>'']);
        \App\Models\Role::create(['role'=>'Account Manager','description'=>'Account Manager has access to the most part of the system in order to perform managerial task.','permission'=>'']);
        \App\Models\Role::create(['role'=>'Regular Employee','description'=>'Regular Employee access are primarily to handle day to day transactions.','permission'=>'']);

        //Execute Seeds for Statuses
        $this->call(StatusSeeder::class);

        //Execute Seeds for States
        $this->call(StateSeeder::class);

        //Execute Seeds for Cities - at least 10 real cities
        $this->call(CitySeeder::class);

        //Execute Seeds for Client Type
        $this->call(ClientTypeSeeder::class);

        //Execute Seeds for Transaction Type
        $this->call(TransactionTypeSeeder::class);

        //Execute Seeds for Compound Periods
        $this->call(CompoundPeriodSeeder::class);

        //Execute Seeds for Organization
        $this->call(OrganizationSeeder::class);

        //Execute Seeds for Users
         \App\Models\User::factory(100)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        //Execute Seeds Client
        $this->call(ClientSeeder::class);

        //Execute Seeds for Term
        $this->call(TermSeeder::class);


        //Execute Seeds for Accounts
        $this->call(AccountSeeder::class);

        //Execute Seeds for Notifications
        $this->call(NotificationSeeder::class);

        //Execute Seeds for AuditTrail
        $this->call(AuditTrailSeeder::class);

        //Execute Seeds for Transactions
        $this->call(TransactionSeeder::class);

    }
}
