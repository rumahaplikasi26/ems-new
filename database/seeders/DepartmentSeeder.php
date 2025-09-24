<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $departmentNames = [
        //     'Human Resources', 'Finance',
        //    'Administration','Project Management'
        // ];

        // foreach ($departmentNames as $departmentName) {
        //     $department = Department::create([
        //         'name' => $departmentName,
        //         'supervisor_id' => Employee::inRandomOrder()->first()->id,
        //         'site_id' => Site::inRandomOrder()->first()->id
        //     ]);
        // }

        // Create departments with supervisors
        $departments = [
            ['name' => 'DIRECTOR', 'site_id' => 1, 'supervisor_id' => null],
            ['name' => 'HRD', 'site_id' => 1, 'supervisor_id' => null], // Will be updated after employees are created
            ['name' => 'IT', 'site_id' => 1, 'supervisor_id' => null], // Will be updated after employees are created
            ['name' => 'FINANCE', 'site_id' => 1, 'supervisor_id' => null], // Will be updated after employees are created
            ['name' => 'OPERATIONAL', 'site_id' => 1, 'supervisor_id' => null], // Will be updated after employees are created
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
