<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobCompletion extends Model
{
    protected $fillable = [
        'application_id',
        'completion_file',
        'admin_verification_status',
        'verified_by',
        'verified_at',
        'rejection_reason',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * Get the application this completion belongs to.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the admin who verified this completion.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if completion is approved.
     */
    public function isApproved(): bool
    {
        return $this->admin_verification_status === 'approved';
    }

    /**
     * Check if completion is pending.
     */
    public function isPending(): bool
    {
        return $this->admin_verification_status === 'pending';
    }

    /**
     * Check if completion is rejected.
     */
    public function isRejected(): bool
    {
        return $this->admin_verification_status === 'rejected';
    }
}
