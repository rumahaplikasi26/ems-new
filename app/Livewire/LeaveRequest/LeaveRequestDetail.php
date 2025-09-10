<?php

namespace App\Livewire\LeaveRequest;

use App\Models\LeaveRequest;
use App\Livewire\BaseComponent;
use Livewire\Attributes\On;
use App\Models\RequestValidate;
use App\Jobs\SendEmailJob;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class LeaveRequestDetail extends BaseComponent
{
    use LivewireAlert;
    public $leave_request, $notes, $start_date, $end_date, $employee_id, $recipients, $isApproved, $current_total_leave, $total_days, $total_leave_after_request, $recipientsWithStatus, $recipientStatus, $isApprovedRecipient;

    public $leave_remaining = 0, $leave_taken = 0, $leave_period = 0;
    public function approveConfirm()
    {
        $this->alert('info', 'Are you sure you want to approve this leave request?', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,

            'showConfirmButton' => true,
            'confirmButtonColor' => '#00a8ff',
            'confirmButtonText' => 'Yes, Approve',
            'cancelButtonText' => 'Cancel',
            'onConfirmed' => 'approve-leave-request',
            'showCancelButton' => true,

            'allowOutsideClick' => false,
            'allowEnterKey' => true,
            'allowEscapeKey' => false,
            'stopKeydownPropagation' => false,
        ]);
    }

    public function rejectConfirm()
    {
        $this->alert('info', 'Are you sure you want to reject this leave request?', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,

            'showConfirmButton' => true,
            'confirmButtonColor' => '#A90C0C',
            'confirmButtonText' => 'Yes, Reject',
            'cancelButtonText' => 'Cancel',
            'onConfirmed' => 'reject-leave-request',
            'showCancelButton' => true,

            'allowOutsideClick' => false,
            'allowEnterKey' => true,
            'allowEscapeKey' => false,
            'stopKeydownPropagation' => false,
        ]);
    }

    #[On('approve-leave-request')]
    public function approve()
    {
        $employeeId = $this->authUser->employee->id;

        // Tambahkan validasi untuk recipient
        RequestValidate::updateOrCreate(
            [
                'validatable_id' => $this->leave_request->id,
                'validatable_type' => LeaveRequest::class,
                'employee_id' => $employeeId,
            ],
            ['status' => 'approved']
        );

        SendEmailJob::dispatch($this->leave_request->employee->user, 'approved-leave-request', ['leave_request' => $this->leave_request], $this->authUser);


        // Periksa dan perbarui status isApproved pada AbsentRequest
        $this->leave_request->checkAndUpdateApprovalStatus();

        createNotification(
            $this->leave_request->employee->user->id,
            'Approved Leave Request',
            'approved-leave-request',
            'Leave Request',
            'Leave Request has been approved',
            route('leave-request.detail', $this->leave_request->id)
        );

        activity()
            ->causedBy($this->authUser) // Pengguna yang melakukan login
            ->withProperties(['ip' => request()->ip()]) // Menyimpan alamat IP
            ->event('approve')
            ->log("{$this->authUser->name} telah approve Leave Request");

        // Update recipient status after approval
        $this->isApprovedRecipient = true;
        
        $this->alert('success', 'Leave Request approved successfully');
        $this->dispatch('refreshIndex');
    }

    #[On('reject-leave-request')]
    public function reject()
    {
        $employeeId = $this->authUser->employee->id;

        // Tambahkan validasi untuk recipient
        RequestValidate::updateOrCreate(
            [
                'validatable_id' => $this->leave_request->id,
                'validatable_type' => LeaveRequest::class,
                'employee_id' => $employeeId,
            ],
            ['status' => 'rejected']
        );

        SendEmailJob::dispatch($this->leave_request->employee->user, 'rejected-leave-request', ['leave_request' => $this->leave_request], $this->authUser);

        // Periksa dan perbarui status isApproved pada AbsentRequest
        $this->leave_request->checkAndUpdateApprovalStatus();
        createNotification(
            $this->leave_request->employee->user->id,
            'Rejected Leave Request',
            'rejected-leave-request',
            'Leave Request',
            'Leave Request has been rejected',
            route('leave-request.detail', $this->leave_request->id)
        );

        activity()
            ->causedBy($this->authUser) // Pengguna yang melakukan login
            ->withProperties(['ip' => request()->ip()]) // Menyimpan alamat IP
            ->event('reject leave request')
            ->log("{$this->authUser->name} telah reject Leave Request");

        // Update recipient status after rejection  
        $this->isApprovedRecipient = true; // Set to true to hide action buttons
        
        $this->alert('success', 'Leave Request rejected successfully');
        $this->dispatch('refreshIndex');
    }

    public function mount($id)
    {
        $this->leave_request = LeaveRequest::with('employee.user', 'recipients.employee.user')->find($id);

        if (!$this->leave_request) {
            return redirect()->route('leave-request.index');
        }

        $this->notes = $this->leave_request->notes;
        $this->isApproved = $this->leave_request->is_approved;
        $this->start_date = $this->leave_request->start_date->format('Y-m-d');
        $this->end_date = $this->leave_request->end_date->format('Y-m-d');
        $this->employee_id = $this->leave_request->employee_id;
        $this->current_total_leave = $this->leave_request->current_total_leave;
        $this->total_days = $this->leave_request->total_days;
        $this->total_leave_after_request = $this->leave_request->total_leave_after_request;
        $this->recipients = $this->leave_request->recipients;
        $this->leave_remaining = $this->leave_request->current_total_leave;
        $this->leave_period = $this->leave_request->total_days;
        $this->getAlreadyTaken();

        // Check if current user is a recipient and their approval status
        $employeeRecipient = $this->authUser->employee->id ?? null;
        $this->recipientStatus = $employeeRecipient ? $this->leave_request->hasRecipient($employeeRecipient) : false;
        $this->isApprovedRecipient = $employeeRecipient ? $this->leave_request->isApprovedByRecipient($employeeRecipient) : false;

        $this->recipientsWithStatus = $this->recipients->map(function ($recipient) {
            $data = $this->leave_request->validates()
                ->where('employee_id', $recipient->employee_id)
                ->first(); // Default to 'pending' if no status found

            $bgClass = match ($data->status ?? 'pending') {
                'approved' => 'bg-info',
                'rejected' => 'bg-danger',
                default => 'bg-warning', // For 'pending'
            };

            return [
                'recipient' => $recipient,
                'status' => $data->status ?? 'pending',
                'bgClass' => $bgClass,
                'created_at' => $data->created_at ?? null,
            ];
        });

        $this->dispatch('change-default');
    }

    public function getAlreadyTaken()
    {
        $this->leave_taken = LeaveRequest::where('employee_id', $this->employee_id)->where('is_approved', true)->get()->sum('total_leave_after_request');
        return $this->leave_taken;
    }

    public function render()
    {
        return view('livewire.leave-request.leave-request-detail')->layout('layouts.app', ['title' => 'Leave Request Detail']);
    }
}
