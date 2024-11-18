<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a sample user
       $admin= User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'), 
            'phone' => '090909', 
            'address' => 'AA', 
        ]);
        $adminRole = Role::where('name', 'Admin')->first();
        $admin->assignRole('Admin');

       
    }
}
