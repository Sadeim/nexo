<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'first_name' => 'أحمد',
            'last_name' => 'السمنة',
            'email' => 'user@gmail.com',
            'phone_code' => '1',
            'phone' => '123456789',
            'password' => bcrypt('123456789')
        ]);
    }
}
