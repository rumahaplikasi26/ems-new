<?php

namespace App\Livewire\OvertimeRequest;

use App\Livewire\BaseComponent;
use Livewire\Component;
use App\Models\Employee;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\OvertimeRequest;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class OvertimeRequestTeam extends BaseComponent
{
    use LivewireAlert, WithPagination;

    public $employee_id = [];
    public $perPage = 10;
    public $start_date;
    public $end_date;
    public $priority = '';
    public $status = '';

    #[Url(except: '')]
    public $search = '';

    public $employees;

    protected $listeners = ['refreshIndex' => '$refresh'];

    public function mount()
    {
        $this->employees = Employee::with('user')->get();
    }

    public function render()
    {
        $employeeId = $this->authUser->employee->id;

        $overtime_requests = OvertimeRequest::with('employee.user', 'recipients.employee.user')->when($this->search, function ($query) {
            $query->whereHas('employee.user', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })->orWhere('reason', 'like', '%' . $this->search . '%');
        })->when($this->employee_id, function ($query) {
            $query->whereIn('employee_id', $this->employee_id);
        })->when($this->start_date, function ($query) {
            $query->whereDate('start_date', '>=', $this->start_date);
        })->when($this->end_date, function ($query) {
            $query->whereDate('start_date', '<=', $this->end_date);
        })->when($this->priority, function ($query) {
            $query->where('priority', $this->priority);
        })->when($this->status !== '', function ($query) {
            $query->where('is_approved', $this->status);
        })->whereHas('recipients', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId);
        });

        $overtime_requests = $overtime_requests->orderBy('created_at', 'desc')->paginate($this->perPage);

        return view('livewire.overtime-request.overtime-request-team', compact('overtime_requests'))->layout('layouts.app', ['title' => 'Team Overtime Request']);
    }
}
