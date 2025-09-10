<?php

namespace App\Livewire\AbsentRequest;

use App\Livewire\BaseComponent;
use App\Models\AbsentRequest;
use App\Models\RequestValidate;
use Livewire\Component; 
use Livewire\Attributes\On;
use App\Jobs\SendEmailJob;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class AbsentRequestDetail extends BaseComponent
{

    public $absent_request, $notes, $start_date, $end_date, $employee_id, $recipients, $recipientsWithStatus, $isApproved, $recipientStatus, $isApprovedRecipient;

    public function mount($id)
    {
        $this->absent_request = AbsentRequest::with('employee.user', 'recipients.employee.user')->find($id);
        $this->notes = $this->absent_request->notes;
        $this->isApproved = $this->absent_request->is_approved;
        $this->start_date = $this->absent_request->start_date->format('Y-m-d');
        $this->end_date = $this->absent_request->end_date->format('Y-m-d');
        $this->employee_id = $this->absent_request->employee_id;
        $this->recipients = $this->absent_request->recipients;

        // Check if current user is a recipient and their approval status
        $employeeRecipient = $this->authUser->employee->id ?? null;
        $this->recipientStatus = $employeeRecipient ? $this->absent_request->hasRecipient($employeeRecipient) : false;
        $this->isApprovedRecipient = $employeeRecipient ? $this->absent_request->isApprovedByRecipient($employeeRecipient) : false;

        $this->recipientsWithStatus = $this->recipients->map(function ($recipient) {
            $data = $this->absent_request->validates()
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

        // dd($this->recipientsWithStatus);
    }

    public function approveConfirm()
    {
        LivewireAlert::title('Approve Absent Request')
            ->warning()
            ->text('Are you sure you want to approve this absent request?')
            ->withConfirmButton()
            ->confirmButtonText('Yes, Approve')
            ->withDenyButton()
            ->denyButtonText('Cancel')
            ->onConfirm('approve-absent-request')
            ->show();
    }

    public function rejectConfirm()
    {
        LivewireAlert::title('Reject Absent Request')
            ->warning()
            ->text('Are you sure you want to reject this absent request?')
            ->withConfirmButton()
            ->confirmButtonText('Yes, Reject')
            ->withDenyButton()
            ->denyButtonText('Cancel')
            ->onConfirm('reject-absent-request')
            ->show();
    }

    #[On('approve-absent-request')]
    public function approve()
    {
        $employeeId = $this->authUser->employee->id;

        // Tambahkan validasi untuk recipient
        RequestValidate::updateOrCreate(
            [
                'validatable_id' => $this->absent_request->id,
                'validatable_type' => AbsentRequest::class,
                'employee_id' => $employeeId,
            ],
            ['status' => 'approved']
        );

        SendEmailJob::dispatch($this->absent_request->employee->user, 'approved-absent-request', ['absent_request' => $this->absent_request], $this->authUser);

        // Periksa dan perbarui status isApproved pada AbsentRequest
        $this->absent_request->checkAndUpdateApprovalStatus();

        createNotification(
            $this->absent_request->employee->user->id,
            'Approved Absent Request',
            'approved-absent-request',
            'Absent Request',
            'Absent Request has been approved',
            route('absent-request.detail', $this->absent_request->id)
        );

        activity()
            ->causedBy($this->authUser) // Pengguna yang melakukan login
            ->withProperties(['ip' => request()->ip()]) // Menyimpan alamat IP
            ->event('approve')
            ->log("{$this->authUser->name} telah approve Absent Request");

        // Update recipient status after approval
        $this->isApprovedRecipient = true;
        
        LivewireAlert::title('Success')
            ->success()
            ->text('Absent Request approved successfully')
            ->show();
        $this->dispatch('refreshIndex');
    }

    #[On('reject-absent-request')]
    public function reject()
    {
        $employeeId = $this->authUser->employee->id;

        // Tambahkan validasi untuk recipient
        RequestValidate::updateOrCreate(
            [
                'validatable_id' => $this->absent_request->id,
                'validatable_type' => AbsentRequest::class,
                'employee_id' => $employeeId,
            ],
            ['status' => 'rejected']
        );

        SendEmailJob::dispatch($this->absent_request->employee->user, 'rejected-absent-request', ['absent_request' => $this->absent_request], $this->authUser);

        // Periksa dan perbarui status isApproved pada AbsentRequest
        $this->absent_request->checkAndUpdateApprovalStatus();
        createNotification(
            $this->absent_request->employee->user->id,
            'Rejected Absent Request',
            'rejected-absent-request',
            'Absent Request',
            'Absent Request has been rejected',
            route('absent-request.detail', $this->absent_request->id)
        );

        activity()
            ->causedBy($this->authUser) // Pengguna yang melakukan login
            ->withProperties(['ip' => request()->ip()]) // Menyimpan alamat IP
            ->event('reject absent request')
            ->log("{$this->authUser->name} telah reject Absent Request");

        // Update recipient status after rejection  
        $this->isApprovedRecipient = true; // Set to true to hide action buttons
        
        LivewireAlert::title('Success')
            ->success()
            ->text('Absent Request rejected successfully')
            ->show();
        $this->dispatch('refreshIndex');
    }

    public function render()
    {
        return view('livewire.absent-request.absent-request-detail')->layout('layouts.app', ['title' => 'Absent Request Detail']);
    }
}
