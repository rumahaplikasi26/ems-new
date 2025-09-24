<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific employees for supervisor roles
        $supervisorEmployees = [
            [
                'name' => 'Febi Namaga',
                'email' => 'febi.namaga@company.com',
                'position' => 'HUMAN RESOURCES MANAGER',
                'role' => 'HR',
            ],
            [
                'name' => 'John Doe',
                'email' => 'john.doe@company.com', 
                'position' => 'IT MANAGER',
                'role' => 'Employee',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@company.com',
                'position' => 'FINANCE DIRECTOR', 
                'role' => 'HR',
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike.johnson@company.com',
                'position' => 'PIC',
                'role' => 'Employee',
            ],
        ];

        foreach ($supervisorEmployees as $employeeData) {
            // Check if user already exists
            $user = \App\Models\User::where('email', $employeeData['email'])->first();
            
            if (!$user) {
                $user = \App\Models\User::factory()->create([
                    'name' => $employeeData['name'],
                    'email' => $employeeData['email'],
                ]);
            }

            $position = \App\Models\Position::where('name', $employeeData['position'])->first();
            
            if ($position) {
                // Check if employee already exists
                $employee = Employee::where('user_id', $user->id)->first();
                
                if (!$employee) {
                    Employee::factory()->create([
                        'user_id' => $user->id,
                        'position_id' => $position->id,
                    ]);
                } else {
                    // Update position if employee exists
                    $employee->update(['position_id' => $position->id]);
                }
            }

            // Assign role if not already assigned
            if (!$user->hasRole($employeeData['role'])) {
                $user->assignRole($employeeData['role']);
            }
        }

        // Create regular employees
        $roles = ['Employee'];
        $positions = \App\Models\Position::whereNotIn('name', [
            'HUMAN RESOURCES MANAGER',
            'IT MANAGER', 
            'FINANCE DIRECTOR',
            'PIC'
        ])->get();

        User::factory(15)->create()->each(function ($user) use ($roles, $positions) {
            $position = $positions->random();
            
            Employee::factory()->create([
                'user_id' => $user->id,
                'position_id' => $position->id,
            ]);

            $user->assignRole($roles[array_rand($roles)]);
        });
    }
}
