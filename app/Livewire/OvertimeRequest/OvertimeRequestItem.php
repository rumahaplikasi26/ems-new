<?php

namespace App\Livewire\OvertimeRequest;

use App\Jobs\SendEmailJob;
use App\Livewire\BaseComponent;
use App\Models\OvertimeRequest;
use App\Models\RequestValidate;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class OvertimeRequestItem extends BaseComponent
{
    use LivewireAlert;
    public $overtime_request;
    public $isApproved = false;
    public $isRejected = false;
    public $totalDays = 0;
    public $disableUpdate = false;
    public $disableUpdateApprove = false;
    public $recipientStatus = false;
    public $isApprovedRecipient = false;

    public function mount(OvertimeRequest $overtime_request)
    {
        $this->overtime_request = $overtime_request;
        
        if ($this->authUser->employee->id != $this->overtime_request->employee_id) {
            $this->disableUpdate = true;
        }

        $this->totalDays = $this->overtime_request->duration_in_days;
        $this->isApproved = $this->overtime_request->is_approved;
        $this->isRejected = $this->overtime_request->isRejectedByRecipients();
    }

    public function deleteConfirm()
    {
        $this->alert('warning', 'Are you sure you want to delete this overtime request?', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,

            'showConfirmButton' => true,
            'confirmButtonColor' => '#DD6B55',
            'confirmButtonText' => 'Yes, Delete',
            'cancelButtonText' => 'No',
            'showCancelButton' => true,

            'onConfirmed' => 'delete',
        ]);
    }

    #[On('delete')]
    public function delete()
    {
        try {
            // Check if user can delete this request
            if ($this->overtime_request->employee_id !== $this->authUser->employee->id) {
                $this->alert('error', 'You are not authorized to delete this request.');
                return;
            }

            if ($this->overtime_request->is_approved) {
                $this->alert('error', 'Cannot delete approved overtime request.');
                return;
            }

            $this->overtime_request->recipients()->delete();
            $this->overtime_request->reads()->delete();
            $this->overtime_request->validates()->delete();
            $this->overtime_request->delete();

            $this->alert('success', 'Overtime request deleted successfully.');
            $this->dispatch('refreshIndex');
        } catch (\Exception $e) {
            $this->alert('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function approveConfirm($id)
    {
        $this->alert('info', 'Are you sure you want to approve this overtime request?', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,

            'showConfirmButton' => true,
            'confirmButtonColor' => '#3085d6',
            'confirmButtonText' => 'Yes, Approve',
            'cancelButtonText' => 'No',
            'showCancelButton' => true,

            'onConfirmed' => 'approve',
        ]);
    }

    #[On('approve')]
    public function approve()
    {
        try {
            $employeeId = $this->authUser->employee->id;

            // Check if user is a recipient
            if (!$this->overtime_request->hasRecipient($employeeId)) {
                $this->alert('error', 'You are not authorized to approve this request.');
                return;
            }

            // Check if already validated
            $existingValidation = $this->overtime_request->validates()
                ->where('employee_id', $employeeId)
                ->first();

            if ($existingValidation) {
                $this->alert('warning', 'You have already validated this request.');
                return;
            }

            // Create validation record
            $this->overtime_request->validates()->create([
                'employee_id' => $employeeId,
                'status' => 'approved',
                'notes' => 'Approved via item action'
            ]);

            // Check and update approval status
            $this->overtime_request->checkAndUpdateApprovalStatus();

            $this->alert('success', 'Overtime request approved successfully.');
            $this->dispatch('refreshIndex');
        } catch (\Exception $e) {
            $this->alert('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function rejectConfirm($id)
    {
        $this->alert('warning', 'Are you sure you want to reject this overtime request?', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,

            'showConfirmButton' => true,
            'confirmButtonColor' => '#DD6B55',
            'confirmButtonText' => 'Yes, Reject',
            'cancelButtonText' => 'No',
            'showCancelButton' => true,

            'onConfirmed' => 'reject',
        ]);
    }

    #[On('reject')]
    public function reject()
    {
        try {
            $employeeId = $this->authUser->employee->id;

            // Check if user is a recipient
            if (!$this->overtime_request->hasRecipient($employeeId)) {
                $this->alert('error', 'You are not authorized to reject this request.');
                return;
            }

            // Check if already validated
            $existingValidation = $this->overtime_request->validates()
                ->where('employee_id', $employeeId)
                ->first();

            if ($existingValidation) {
                $this->alert('warning', 'You have already validated this request.');
                return;
            }

            // Create validation record
            $this->overtime_request->validates()->create([
                'employee_id' => $employeeId,
                'status' => 'rejected',
                'notes' => 'Rejected via item action'
            ]);

            $this->alert('success', 'Overtime request rejected.');
            $this->dispatch('refreshIndex');
        } catch (\Exception $e) {
            $this->alert('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $employee = $this->overtime_request->employee;
        $user = $employee->user;
        $recipients = $this->overtime_request->recipients;

        $employeeRecipient = $this->authUser->employee->id;
        $this->recipientStatus = $this->overtime_request->hasRecipient($employeeRecipient);
        $this->isApprovedRecipient = $this->overtime_request->isApprovedByRecipient($employeeRecipient);
        $this->isRejected = $this->overtime_request->isRejectedByRecipients();

        $this->disableUpdateApprove = $this->isApprovedRecipient;
         // Buat array dengan status validasi untuk setiap recipient
         $recipientsWithStatus = $recipients->map(function ($recipient) {
            $status = $this->overtime_request->validates()
                ->where('employee_id', $recipient->employee_id)
                ->first()
                ->status ?? 'pending'; // Default to 'pending' if no status found

            if($status == 'approved') {
                $this->disableUpdate = true;
            }

            $badgeClass = match ($status) {
                'approved' => 'badge-soft-info',
                'rejected' => 'badge-soft-danger',
                default => 'badge-soft-warning', // For 'pending'
            };

            return [
                'recipient' => $recipient,
                'status' => $status,
                'badgeClass' => $badgeClass,
            ];
        });

        return view('livewire.overtime-request.overtime-request-item', [
            'overtime_request' => $this->overtime_request,
            'user' => $user,
            'recipientsWithStatus' => $recipientsWithStatus,
        ]);
    }
}
