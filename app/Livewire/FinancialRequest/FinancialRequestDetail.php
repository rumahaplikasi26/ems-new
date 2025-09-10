<?php

namespace App\Livewire\FinancialRequest;

use App\Livewire\BaseComponent;
use Livewire\Attributes\On;
use App\Models\RequestValidate;
use App\Jobs\SendEmailJob;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\FinancialRequest;

class FinancialRequestDetail extends BaseComponent
{
    use LivewireAlert;
    public $financial_request;
    public $employee_id, $financial_type_id, $title, $amount, $notes, $receipt_image_path, $receipt_image_url, $isApproved, $recipients, $recipientsWithStatus, $financialType, $recipientStatus, $isApprovedRecipient;

    public function approveConfirm()
    {
        $this->alert('info', 'Are you sure you want to approve this financial request?', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,

            'showConfirmButton' => true,
            'confirmButtonColor' => '#00a8ff',
            'confirmButtonText' => 'Yes, Approve',
            'cancelButtonText' => 'Cancel',
            'onConfirmed' => 'approve-financial-request',
            'showCancelButton' => true,

            'allowOutsideClick' => false,
            'allowEnterKey' => true,
            'allowEscapeKey' => false,
            'stopKeydownPropagation' => false,
        ]);
    }

    public function rejectConfirm()
    {
        $this->alert('info', 'Are you sure you want to reject this financial request?', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,

            'showConfirmButton' => true,
            'confirmButtonColor' => '#A90C0C',
            'confirmButtonText' => 'Yes, Reject',
            'cancelButtonText' => 'Cancel',
            'onConfirmed' => 'reject-financial-request',
            'showCancelButton' => true,

            'allowOutsideClick' => false,
            'allowEnterKey' => true,
            'allowEscapeKey' => false,
            'stopKeydownPropagation' => false,
        ]);
    }

    #[On('approve-financial-request')]
    public function approve()
    {
        $employeeId = $this->authUser->employee->id;

        // Tambahkan validasi untuk recipient
        RequestValidate::updateOrCreate(
            [
                'validatable_id' => $this->financial_request->id,
                'validatable_type' => FinancialRequest::class,
                'employee_id' => $employeeId,
            ],
            ['status' => 'approved']
        );

        SendEmailJob::dispatch($this->financial_request->employee->user, 'approved-financial-request', ['financial_request' => $this->financial_request], $this->authUser);

        // Periksa dan perbarui status isApproved pada AbsentRequest
        $this->financial_request->checkAndUpdateApprovalStatus();

        createNotification(
            $this->financial_request->employee->user->id,
            'Approved Financial Request',
            'approve-financial-request',
            'Financial Request',
            'Financial Request has been approve',
            route('financial-request.detail', $this->financial_request->id)
        );

        activity()
            ->causedBy($this->authUser) // Pengguna yang melakukan login
            ->withProperties(['ip' => request()->ip()]) // Menyimpan alamat IP
            ->event('approve')
            ->log("{$this->authUser->name} telah approve Financial Request");

        $this->alert('success', 'Financial Request approved successfully');
        $this->dispatch('refreshIndex');
    }

    #[On('reject-financial-request')]
    public function reject()
    {
        $employeeId = $this->authUser->employee->id;

        // Tambahkan validasi untuk recipient
        RequestValidate::updateOrCreate(
            [
                'validatable_id' => $this->financial_request->id,
                'validatable_type' => FinancialRequest::class,
                'employee_id' => $employeeId,
            ],
            ['status' => 'rejected']
        );

        SendEmailJob::dispatch($this->financial_request->employee->user, 'rejected-financial-request', ['financial_request' => $this->financial_request], $this->authUser);

        // Periksa dan perbarui status isApproved pada AbsentRequest
        $this->financial_request->checkAndUpdateApprovalStatus();
        createNotification(
            $this->financial_request->employee->user->id,
            'Rejected Financial Request',
            'rejected-financial-request',
            'Financial Request',
            'Financial Request has been rejected',
            route('financial-request.detail', $this->financial_request->id)
        );

        activity()
            ->causedBy($this->authUser) // Pengguna yang melakukan login
            ->withProperties(['ip' => request()->ip()]) // Menyimpan alamat IP
            ->event('reject financial request')
            ->log("{$this->authUser->name} telah reject Financial Request");

        $this->alert('success', 'Financial Request rejected successfully');
        $this->dispatch('refreshIndex');
    }

    public function mount($id)
    {
        $this->financial_request = \App\Models\FinancialRequest::find($id);

        if(!$this->financial_request) {
            return redirect()->route('financial-request.index');
        }

        $this->isApproved = $this->financial_request->is_approved;
        $this->employee_id = $this->financial_request->employee_id;
        $this->financial_type_id = $this->financial_request->financial_type_id;
        $this->title = $this->financial_request->title;
        $this->amount = $this->financial_request->amount;
        $this->financialType = $this->financial_request->financialType;
        $this->notes = $this->financial_request->notes;
        $this->receipt_image_path = $this->financial_request->receipt_image_path;
        $this->receipt_image_url = $this->financial_request->receipt_image_url;
        $this->recipients = $this->financial_request->recipients;

        // Check if current user is a recipient and their approval status
        $employeeRecipient = $this->authUser->employee->id ?? null;
        $this->recipientStatus = $employeeRecipient ? $this->financial_request->hasRecipient($employeeRecipient) : false;
        $this->isApprovedRecipient = $employeeRecipient ? $this->financial_request->isApprovedByRecipient($employeeRecipient) : false;

        $this->recipientsWithStatus = $this->recipients->map(function ($recipient) {
            $data = $this->financial_request->validates()
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
    }

    public function render()
    {
        return view('livewire.financial-request.financial-request-detail')->layout('layouts.app', ['title' => 'Financial Request Detail']);
    }
}
