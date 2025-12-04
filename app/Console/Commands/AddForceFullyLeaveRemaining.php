<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;

class AddForceFullyLeaveRemaining extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-force-fully-leave-remaining {--leave-remaining= : The leave remaining to add};';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add force fully leave remaining for each employee';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $leaveRemaining = $this->option('leave-remaining');

        $employees = Employee::all();

        foreach ($employees as $employee) {
            $employee->update([
                'leave_remaining' => $employee->leave_remaining + $leaveRemaining,
            ]);
        }
    }
}
