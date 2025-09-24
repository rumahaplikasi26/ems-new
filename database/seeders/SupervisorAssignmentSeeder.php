<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Seeder;

class SupervisorAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assign supervisors to departments
        $supervisorAssignments = [
            'HRD' => 'HUMAN RESOURCES MANAGER',
            'IT' => 'IT MANAGER', 
            'FINANCE' => 'FINANCE DIRECTOR',
            'OPERATIONAL' => 'PIC',
        ];

        foreach ($supervisorAssignments as $departmentName => $positionName) {
            $department = Department::where('name', $departmentName)->first();
            $position = Position::where('name', $positionName)->first();
            
            if ($department && $position) {
                // Find an employee with this position
                $supervisor = Employee::where('position_id', $position->id)->first();
                
                if ($supervisor) {
                    $department->update(['supervisor_id' => $supervisor->id]);
                    echo "Assigned {$supervisor->user->name} as supervisor for {$departmentName}\n";
                } else {
                    echo "No employee found for position {$positionName} in {$departmentName}\n";
                }
            } else {
                echo "Department {$departmentName} or Position {$positionName} not found\n";
            }
        }
    }
}
