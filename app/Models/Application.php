<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $table = 'job_applications';

    protected $fillable = [
        'student_id',
        'job_id',
        'cover_letter',
        'cv_file',
        'status',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Get the student (user) that made this application.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the job that was applied to.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    /**
     * Get the completion submission for this application.
     */
    public function completion()
    {
        return $this->hasOne(JobCompletion::class);
    }

    /**
     * Get the invoice for this application.
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Get the rating for this application.
     */
    public function rating()
    {
        return $this->hasOne(Rating::class);
    }
}
