<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Job extends Model
{
    protected $table = 'job_postings';

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'budget',
        'status',
        'category',
        'start_day',
        'end_day',
        'workers_needed',
        'work_type',
    ];

    protected $casts = [
        'start_day' => 'date',
        'end_day' => 'date',
        'budget' => 'decimal:2',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Get the client (user) that posted this job.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the applications for this job.
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'job_id');
    }

    /**
     * Get the invoices for this job.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the number of accepted applications.
     */
    public function acceptedApplicationsCount()
    {
        return $this->applications()->where('status', 'accepted')->count();
    }

    /**
     * Check if job has reached worker limit.
     */
    public function hasReachedWorkerLimit()
    {
        return $this->acceptedApplicationsCount() >= $this->workers_needed;
    }
}
