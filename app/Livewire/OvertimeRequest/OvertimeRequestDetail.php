<?php

namespace App\Livewire\OvertimeRequest;

use App\Livewire\BaseComponent;
use App\Models\OvertimeRequest;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use App\Models\RequestValidate;
use App\Jobs\SendEmailJob;

class OvertimeRequestDetail extends BaseComponent
{
    use LivewireAlert;

    public $overtime_request;
    public $action = '';
    public $notes = '';
    public $recipientStatus = false;
    public $isApprovedRecipient = false;

    public function mount($id)
    {
        $this->overtime_request = OvertimeRequest::with('employee.user', 'recipients.employee.user', 'validates.employee.user')->findOrFail($id);

        if (!$this->overtime_request) {
            return redirect()->route('overtime-request.index');
        }

        // Check if current user is a recipient and their approval status
        $employeeRecipient = $this->authUser->employee->id ?? null;
        $this->recipientStatus = $employeeRecipient ? $this->overtime_request->hasRecipient($employeeRecipient) : false;
        $this->isApprovedRecipient = $employeeRecipient ? $this->overtime_request->isApprovedByRecipient($employeeRecipient) : false;

        // Mark as read
        if (!$this->overtime_request->reads()->where('employee_id', $this->authUser->employee->id)->exists()) {
            $this->overtime_request->reads()->create([
                'employee_id' => $this->authUser->employee->id
            ]);
        }
    }

    public function approveConfirm()
    {
        $this->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            // Check if already validated
            $existingValidation = $this->overtime_request->validates()
                ->where('employee_id', $this->authUser->employee->id)
                ->first();

            if ($existingValidation) {
                $this->alert('warning', 'You have already validated this request.');
                return;
            }

            // Create validation record
            $this->overtime_request->validates()->create([
                'employee_id' => $this->authUser->employee->id,
                'status' => 'approved',
                'notes' => $this->notes
            ]);

            // Check and update approval status
            $this->overtime_request->checkAndUpdateApprovalStatus();

            // Send email notification
            SendEmailJob::dispatch($this->overtime_request->employee->user, 'approved-overtime-request', ['overtime_request' => $this->overtime_request], $this->authUser);

            // Create notification
            createNotification(
                $this->overtime_request->employee->user->id,
                'Approved Overtime Request',
                'approved-overtime-request',
                'Overtime Request',
                'Overtime Request has been approved',
                route('overtime-request.detail', $this->overtime_request->id)
            );

            // Log activity
            activity()
                ->causedBy($this->authUser)
                ->withProperties(['ip' => request()->ip()])
                ->event('approve')
                ->log("{$this->authUser->name} telah approve Overtime Request");

            // Update recipient status after approval
            $this->isApprovedRecipient = true;
            
            $this->alert('success', 'Overtime request approved successfully.');
            $this->overtime_request->refresh();
            $this->notes = '';
        } catch (\Exception $e) {
            $this->alert('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function rejectConfirm()
    {
        $this->validate([
            'notes' => 'required|string|max:500'
        ], [
            'notes.required' => 'Please provide a reason for rejection.'
        ]);

        try {
            // Check if already validated
            $existingValidation = $this->overtime_request->validates()
                ->where('employee_id', $this->authUser->employee->id)
                ->first();

            if ($existingValidation) {
                $this->alert('warning', 'You have already validated this request.');
                return;
            }

            // Create validation record
            $this->overtime_request->validates()->create([
                'employee_id' => $this->authUser->employee->id,
                'status' => 'rejected',
                'notes' => $this->notes
            ]);

            // Send email notification
            SendEmailJob::dispatch($this->overtime_request->employee->user, 'rejected-overtime-request', ['overtime_request' => $this->overtime_request], $this->authUser);

            // Create notification
            createNotification(
                $this->overtime_request->employee->user->id,
                'Rejected Overtime Request',
                'rejected-overtime-request',
                'Overtime Request',
                'Overtime Request has been rejected',
                route('overtime-request.detail', $this->overtime_request->id)
            );

            // Log activity
            activity()
                ->causedBy($this->authUser)
                ->withProperties(['ip' => request()->ip()])
                ->event('reject overtime request')
                ->log("{$this->authUser->name} telah reject Overtime Request");

            // Update recipient status after rejection  
            $this->isApprovedRecipient = true; // Set to true to hide action buttons
            
            $this->alert('success', 'Overtime request rejected.');
            $this->overtime_request->refresh();
            $this->notes = '';
        } catch (\Exception $e) {
            $this->alert('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.overtime-request.overtime-request-detail')->layout('layouts.app', ['title' => 'Overtime Request Detail']);
    }
}
