<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;

class AddLeaveRemaining extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-leave-remaining';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add leave remaining for each employee every date 1st of each month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $employees = Employee::all();

        foreach ($employees as $employee) {
            $employee->update([
                'leave_remaining' => $employee->leave_remaining + 1,
            ]);
        }
    }
}
