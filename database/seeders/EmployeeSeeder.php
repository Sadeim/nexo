<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            ['name' => 'Apood', 'sort_order' => 1],
            ['name' => 'Sam', 'sort_order' => 2],
            ['name' => 'George', 'sort_order' => 3],
            ['name' => 'Joe', 'sort_order' => 4],
        ];

        foreach ($employees as $data) {
            Employee::firstOrCreate(
                ['name' => $data['name']],
                ['is_active' => true, 'sort_order' => $data['sort_order']],
            );
        }
    }
}
