<?php

namespace App\Livewire\OvertimeRequest;

use App\Jobs\SendEmailJob;
use App\Livewire\BaseComponent;
use App\Models\OvertimeRequest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class OvertimeRequestForm extends BaseComponent
{
    use LivewireAlert;

    public $mode = 'Create';
    public $overtime_request;
    public $reason, $employee_id, $start_date, $end_date, $priority = 'minor', $recipients = [];
    public $employee;
    public $employees;

    public function mount($id = null)
    {
        $this->employees = Employee::with('user')->whereNot('user_id', $this->authUser->id)->get();

        if ($id) {
            $this->mode = 'Edit';
            $this->overtime_request = OvertimeRequest::find($id);
            $this->employee = $this->overtime_request->employee;
            $this->reason = $this->overtime_request->reason;
            $this->employee_id = $this->overtime_request->employee_id;
            $this->start_date = $this->overtime_request->start_date->format('Y-m-d\TH:i');
            $this->end_date = $this->overtime_request->end_date->format('Y-m-d\TH:i');
            $this->priority = $this->overtime_request->priority;
            $this->recipients = $this->overtime_request->recipients->pluck('employee_id')->toArray();

            $this->dispatch('set-default-form', param: 'recipients', value: $this->recipients);
        } else {
            $this->employee = $this->authUser->employee;

            $this->mode = 'Create';
            $this->reason = '';
            $this->employee_id = $this->employee->id;
            $this->start_date = '';
            $this->end_date = '';
            $this->priority = 'minor';
        }
    }

    #[On('change-input-form')]
    public function changeInputForm($param, $value)
    {
        $this->$param = $value;
    }

    public function save()
    {
        $this->validate([
            'reason' => 'required|string|max:1000',
            'start_date' => 'required|date|after_or_equal:now',
            'end_date' => 'required|date|after:start_date',
            'priority' => 'required|in:minor,major',
            'recipients' => 'required|array|min:1'
        ], [
            'reason.required' => 'Reason field is required.',
            'start_date.required' => 'Start date field is required.',
            'start_date.after_or_equal' => 'Start date must be now or later.',
            'end_date.required' => 'End date field is required.',
            'end_date.after' => 'End date must be after start date.',
            'priority.required' => 'Priority field is required.',
            'recipients.required' => 'At least one recipient must be selected.',
            'recipients.min' => 'At least one recipient must be selected.'
        ]);

        try {
            if ($this->mode == 'Create') {
                $overtime_request = OvertimeRequest::create([
                    'employee_id' => $this->employee_id,
                    'reason' => $this->reason,
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'priority' => $this->priority,
                    'is_approved' => false,
                ]);

                // Add recipients
                foreach ($this->recipients as $recipient) {
                    $overtime_request->recipients()->create([
                        'employee_id' => $recipient
                    ]);
                }

                // Send notification emails
                $this->sendNotificationEmails($overtime_request);

                $this->alert('success', 'Overtime request created successfully.');
                return redirect()->route('overtime-request.index');
            } else {
                $this->overtime_request->update([
                    'reason' => $this->reason,
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'priority' => $this->priority,
                ]);

                // Update recipients
                $this->overtime_request->recipients()->delete();
                foreach ($this->recipients as $recipient) {
                    $this->overtime_request->recipients()->create([
                        'employee_id' => $recipient
                    ]);
                }

                $this->alert('success', 'Overtime request updated successfully.');
                return redirect()->route('overtime-request.index');
            }
        } catch (\Exception $e) {
            $this->alert('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    private function sendNotificationEmails($overtime_request)
    {
        foreach ($this->recipients as $recipientId) {
            $recipient = Employee::with('user')->find($recipientId);
            if ($recipient && $recipient->user->email) {
                $emailData = [
                    'recipient_name' => $recipient->user->name,
                    'requester_name' => $this->employee->user->name,
                    'start_date' => Carbon::parse($this->start_date)->format('d F Y, H:i'),
                    'end_date' => Carbon::parse($this->end_date)->format('d F Y, H:i'),
                    'priority' => ucfirst($this->priority),
                    'reason' => $this->reason,
                    'request_url' => route('overtime-request.detail', $overtime_request->id)
                ];

                SendEmailJob::dispatch($recipient->user, 'sender-overtime-request', ['overtime_request' => $overtime_request]);
            }
        }
    }

    public function render()
    {
        return view('livewire.overtime-request.overtime-request-form')->layout('layouts.app', ['title' => $this->mode . ' Overtime Request']);
    }
}
