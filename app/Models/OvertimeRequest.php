<?php

namespace App\Models;

use App\Models\RequestRead;
use App\Models\RequestValidate;
use App\Models\RequestRecipient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class OvertimeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'reason',
        'start_date',
        'end_date',
        'priority',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the employee that owns the OvertimeRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get all of the recipients for the OvertimeRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function recipients(): MorphMany
    {
        return $this->morphMany(RequestRecipient::class, 'recipientable');
    }

    /**
     * Check if the employee with given id is a recipient of this request
     *
     * @param int $employeeId
     * @return bool
     */
    public function hasRecipient($employeeId): bool
    {
        return $this->recipients()->where('employee_id', $employeeId)->exists();
    }

    /**
     * Get all of the read for the OvertimeRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reads(): MorphMany
    {
        return $this->morphMany(RequestRead::class, 'readable');
    }

    /**
     * Get all of the validates for the OvertimeRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function validates(): MorphMany
    {
        return $this->morphMany(RequestValidate::class, 'validatable');
    }

    public function isApprovedByRecipient($employeeId): bool
    {
        return $this->validates()->where('employee_id', $employeeId)->where('status', 'approved')->exists();
    }

    public function checkAndUpdateApprovalStatus()
    {
        // Jumlah recipients terkait dengan OvertimeRequest ini
        $totalRecipients = $this->recipients()->count();

        // Jumlah recipients yang sudah approve
        $approvedRecipients = $this->validates()->where('status', 'approved')->count();

        // Jika jumlah recipients yang sudah approve sama dengan total recipients, maka isApproved menjadi true
        $isApproved = $totalRecipients > 0 && $approvedRecipients === $totalRecipients;

        // Perbarui status isApproved pada OvertimeRequest
        $this->update(['is_approved' => $isApproved]);
    }

    public function isRejectedByRecipients(): bool
    {
        $totalRecipients = $this->recipients()->count();
        $rejectedRecipients = $this->validates()->where('status', 'rejected')->count();
        return $totalRecipients > 0 && $rejectedRecipients === $totalRecipients;
    }

    /**
     * Get the duration in hours
     *
     * @return float
     */
    public function getDurationAttribute()
    {
        if ($this->start_date && $this->end_date) {
            $start = \Carbon\Carbon::parse($this->start_date);
            $end = \Carbon\Carbon::parse($this->end_date);
            return $start->diffInHours($end, true);
        }
        return 0;
    }

    /**
     * Get the duration in days
     *
     * @return int
     */
    public function getDurationInDaysAttribute()
    {
        if ($this->start_date && $this->end_date) {
            $start = \Carbon\Carbon::parse($this->start_date)->startOfDay();
            $end = \Carbon\Carbon::parse($this->end_date)->startOfDay();
            return $start->diffInDays($end) + 1;
        }
        return 0;
    }
}
