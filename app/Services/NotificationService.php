<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a notification for a user.
     */
    public static function create(User $user, string $type, string $title, string $message, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Notify student when application is accepted.
     */
    public static function applicationAccepted(User $student, $application, $job)
    {
        return self::create(
            $student,
            'application_accepted',
            'Application Accepted! 🎉',
            "Your application for \"{$job->title}\" has been accepted!",
            [
                'application_id' => $application->id,
                'job_id' => $job->id,
                'job_title' => $job->title,
            ]
        );
    }

    /**
     * Notify student when application is rejected.
     */
    public static function applicationRejected(User $student, $application, $job)
    {
        return self::create(
            $student,
            'application_rejected',
            'Application Status Update',
            "Unfortunately, your application for \"{$job->title}\" was not selected.",
            [
                'application_id' => $application->id,
                'job_id' => $job->id,
                'job_title' => $job->title,
            ]
        );
    }

    /**
     * Notify student when verification is approved.
     */
    public static function verificationApproved(User $student)
    {
        return self::create(
            $student,
            'verification_approved',
            'Account Verified ✅',
            'Your account has been verified! You can now enjoy all platform features.',
            []
        );
    }

    /**
     * Notify student when verification is rejected.
     */
    public static function verificationRejected(User $student, string $reason)
    {
        return self::create(
            $student,
            'verification_rejected',
            'Verification Rejected',
            "Your verification was rejected. Reason: {$reason}",
            [
                'rejection_reason' => $reason,
            ]
        );
    }

    /**
     * Notify client when a new application is received.
     */
    public static function newApplication(User $client, $application, $job)
    {
        return self::create(
            $client,
            'new_application',
            'New Application Received',
            "{$application->student->name} applied for \"{$job->title}\"",
            [
                'application_id' => $application->id,
                'job_id' => $job->id,
                'job_title' => $job->title,
                'student_id' => $application->student->id,
                'student_name' => $application->student->name,
            ]
        );
    }

    /**
     * Notify student when job completion is approved.
     */
    public static function jobCompletionApproved(User $student, $completion, $job)
    {
        return self::create(
            $student,
            'job_completion_approved',
            'Job Completed Successfully! 🎊',
            "Your completion for \"{$job->title}\" has been approved by the admin.",
            [
                'completion_id' => $completion->id,
                'job_id' => $job->id,
                'job_title' => $job->title,
            ]
        );
    }

    /**
     * Notify student when job completion is rejected.
     */
    public static function jobCompletionRejected(User $student, $completion, $job, string $reason)
    {
        return self::create(
            $student,
            'job_completion_rejected',
            'Job Completion Rejected',
            "Your completion for \"{$job->title}\" was rejected. Reason: {$reason}",
            [
                'completion_id' => $completion->id,
                'job_id' => $job->id,
                'job_title' => $job->title,
                'rejection_reason' => $reason,
            ]
        );
    }

    /**
     * Notify student when they receive a rating.
     */
    public static function newRating(User $student, $rating, $job)
    {
        return self::create(
            $student,
            'new_rating',
            'New Rating Received ⭐',
            "You received a {$rating->rating}-star rating for \"{$job->title}\"",
            [
                'rating_id' => $rating->id,
                'job_id' => $job->id,
                'job_title' => $job->title,
                'rating' => $rating->rating,
            ]
        );
    }

    /**
     * Notify client when invoice is created.
     */
    public static function invoiceCreated(User $client, $invoice, $job)
    {
        return self::create(
            $client,
            'invoice_created',
            'New Invoice Generated',
            "An invoice has been generated for \"{$job->title}\" (Total: $" . number_format($invoice->total_amount, 2) . ")",
            [
                'invoice_id' => $invoice->id,
                'job_id' => $job->id,
                'job_title' => $job->title,
                'total_amount' => $invoice->total_amount,
            ]
        );
    }
}






