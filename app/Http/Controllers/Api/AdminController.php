<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Job;
use App\Models\JobCompletion;
use App\Models\Invoice;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Get all students with pending verification.
     */
    public function pendingVerifications()
    {
        $students = User::where('role', 'student')
            ->where('verification_status', 'pending')
            ->whereNotNull('identity_card')
            ->whereNotNull('student_card')
            ->with('verifier')
            ->latest()
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'university' => $student->university,
                    'identity_card_number' => $student->identity_card_number,
                    'identity_card_url' => $student->identity_card ? Storage::url($student->identity_card) : null,
                    'student_card_url' => $student->student_card ? Storage::url($student->student_card) : null,
                    'cv_file_url' => $student->cv_file ? Storage::url($student->cv_file) : null,
                    'verification_status' => $student->verification_status,
                    'rejection_reason' => $student->rejection_reason,
                    'created_at' => $student->created_at,
                    'updated_at' => $student->updated_at,
                ];
            });

        return response()->json([
            'students' => $students,
            'count' => $students->count(),
        ]);
    }

    /**
     * Get all students with their verification status.
     */
    public function allStudents(Request $request)
    {
        $query = User::where('role', 'student')->with('verifier');

        if ($request->has('status')) {
            $query->where('verification_status', $request->status);
        }

        $students = $query->latest()->paginate(20)->through(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'university' => $student->university,
                'identity_card_number' => $student->identity_card_number,
                'identity_card_url' => $student->identity_card ? Storage::url($student->identity_card) : null,
                'student_card_url' => $student->student_card ? Storage::url($student->student_card) : null,
                'cv_file_url' => $student->cv_file ? Storage::url($student->cv_file) : null,
                'verification_status' => $student->verification_status,
                'rejection_reason' => $student->rejection_reason,
                'verified_at' => $student->verified_at,
                'verified_by' => $student->verifier ? $student->verifier->name : null,
                'banned_at' => $student->banned_at,
                'ban_reason' => $student->ban_reason,
                'created_at' => $student->created_at,
                'updated_at' => $student->updated_at,
            ];
        });

        return response()->json($students);
    }

    /**
     * Get all clients.
     */
    public function allClients(Request $request)
    {
        $query = User::where('role', 'client');

        $clients = $query->latest()->paginate(20)->through(function ($client) {
            return [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'banned_at' => $client->banned_at,
                'ban_reason' => $client->ban_reason,
                'created_at' => $client->created_at,
                'updated_at' => $client->updated_at,
            ];
        });

        return response()->json($clients);
    }

    /**
     * Ban a user (student or client). Cannot ban admin or self.
     */
    public function banUser(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Cannot ban an admin.'], 403);
        }
        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Cannot ban yourself.'], 403);
        }

        $validated = $request->validate([
            'ban_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update([
            'banned_at' => now(),
            'ban_reason' => $validated['ban_reason'] ?? null,
        ]);

        // Revoke all tokens so banned user is logged out
        $user->tokens()->delete();

        return response()->json([
            'message' => 'User has been banned.',
            'user' => [
                'id' => $user->id,
                'banned_at' => $user->banned_at,
                'ban_reason' => $user->ban_reason,
            ],
        ]);
    }

    /**
     * Unban a user.
     */
    public function unbanUser(User $user)
    {
        $user->update([
            'banned_at' => null,
            'ban_reason' => null,
        ]);

        return response()->json([
            'message' => 'User has been unbanned.',
            'user' => [
                'id' => $user->id,
                'banned_at' => null,
            ],
        ]);
    }

    /**
     * Soft-delete a user. Cannot delete admin or self.
     */
    public function deleteUser(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Cannot delete an admin.'], 403);
        }
        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'Cannot delete yourself.'], 403);
        }

        $user->tokens()->delete();
        $user->delete(); // soft delete

        return response()->json([
            'message' => 'User has been deleted.',
        ]);
    }

    /**
     * Approve a student's verification.
     */
    public function approveStudent(User $user)
    {
        if ($user->role !== 'student') {
            return response()->json(['message' => 'User is not a student.'], 400);
        }

        $user->update([
            'verification_status' => 'approved',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'rejection_reason' => null, // Clear rejection reason on approval
        ]);

        // Notify student
        NotificationService::verificationApproved($user);

        return response()->json([
            'message' => 'Student verification approved successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'verification_status' => $user->verification_status,
                'verified_at' => $user->verified_at,
            ],
        ]);
    }

    /**
     * Reject a student's verification.
     */
    public function rejectStudent(Request $request, User $user)
    {
        if ($user->role !== 'student') {
            return response()->json(['message' => 'User is not a student.'], 400);
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $user->update([
            'verification_status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Notify student
        NotificationService::verificationRejected($user, $validated['rejection_reason']);

        return response()->json([
            'message' => 'Student verification rejected.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'verification_status' => $user->verification_status,
                'verified_at' => $user->verified_at,
                'rejection_reason' => $user->rejection_reason,
            ],
        ]);
    }

    /**
     * Get verification statistics.
     */
    public function verificationStats()
    {
        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'pending' => User::where('role', 'student')->where('verification_status', 'pending')->count(),
            'approved' => User::where('role', 'student')->where('verification_status', 'approved')->count(),
            'rejected' => User::where('role', 'student')->where('verification_status', 'rejected')->count(),
            'with_documents' => User::where('role', 'student')
                ->whereNotNull('identity_card')
                ->whereNotNull('student_card')
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get all pending job completions.
     */
    public function pendingCompletions()
    {
        $completions = JobCompletion::where('admin_verification_status', 'pending')
            ->with(['application.student', 'application.job.client'])
            ->latest()
            ->get()
            ->map(function ($completion) {
                return [
                    'id' => $completion->id,
                    'application_id' => $completion->application_id,
                    'student' => [
                        'id' => $completion->application->student->id,
                        'name' => $completion->application->student->name,
                        'email' => $completion->application->student->email,
                    ],
                    'job' => [
                        'id' => $completion->application->job->id,
                        'title' => $completion->application->job->title,
                        'client_name' => $completion->application->job->client->name,
                    ],
                    'completion_file_url' => Storage::url($completion->completion_file),
                    'admin_verification_status' => $completion->admin_verification_status,
                    'created_at' => $completion->created_at,
                    'updated_at' => $completion->updated_at,
                ];
            });

        return response()->json([
            'completions' => $completions,
            'count' => $completions->count(),
        ]);
    }

    /**
     * Get all job completions.
     */
    public function allCompletions(Request $request)
    {
        $query = JobCompletion::with(['application.student', 'application.job.client', 'verifier']);

        if ($request->has('status')) {
            $query->where('admin_verification_status', $request->status);
        }

        $completions = $query->latest()->paginate(20)->through(function ($completion) {
            return [
                'id' => $completion->id,
                'application_id' => $completion->application_id,
                'student' => [
                    'id' => $completion->application->student->id,
                    'name' => $completion->application->student->name,
                    'email' => $completion->application->student->email,
                ],
                'job' => [
                    'id' => $completion->application->job->id,
                    'title' => $completion->application->job->title,
                    'client_name' => $completion->application->job->client->name,
                ],
                'completion_file_url' => Storage::url($completion->completion_file),
                'admin_verification_status' => $completion->admin_verification_status,
                'rejection_reason' => $completion->rejection_reason,
                'verified_at' => $completion->verified_at,
                'verified_by' => $completion->verifier ? $completion->verifier->name : null,
                'created_at' => $completion->created_at,
                'updated_at' => $completion->updated_at,
            ];
        });

        return response()->json($completions);
    }

    /**
     * Approve a job completion.
     */
    public function approveCompletion(JobCompletion $completion)
    {
        $completion->update([
            'admin_verification_status' => 'approved',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'rejection_reason' => null,
        ]);

        $job = $completion->application->job;
        $application = $completion->application;

        // Update job status to completed
        $job->update(['status' => 'completed']);

        // Create invoice for the client
        // Only create invoice if job has a budget
        $invoice = null;
        if ($job->budget && $job->budget > 0) {
            $totalAmount = $job->budget;
            $commissionRate = 10.00; // 10% commission
            $commissionAmount = ($totalAmount * $commissionRate) / 100;
            $studentAmount = $totalAmount - $commissionAmount;

            $invoice = Invoice::create([
                'job_id' => $job->id,
                'application_id' => $application->id,
                'client_id' => $job->client_id,
                'student_id' => $application->student_id,
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'total_amount' => $totalAmount,
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount,
                'student_amount' => $studentAmount,
                'status' => 'sent',
                'issued_at' => now(),
            ]);

            // Notify client about invoice
            NotificationService::invoiceCreated($job->client, $invoice, $job);
        }

        // Notify student about completion approval
        NotificationService::jobCompletionApproved($application->student, $completion, $job);

        return response()->json([
            'message' => 'Job completion approved successfully.',
            'completion' => [
                'id' => $completion->id,
                'admin_verification_status' => $completion->admin_verification_status,
                'verified_at' => $completion->verified_at,
            ],
            'invoice' => $invoice ? [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'total_amount' => $invoice->total_amount,
                'commission_amount' => $invoice->commission_amount,
                'student_amount' => $invoice->student_amount,
            ] : null,
        ]);
    }

    /**
     * Reject a job completion.
     */
    public function rejectCompletion(Request $request, JobCompletion $completion)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $completion->update([
            'admin_verification_status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Load relationships for notification
        $completion->load('application.job', 'application.student');

        // Notify student
        NotificationService::jobCompletionRejected($completion->application->student, $completion, $completion->application->job, $validated['rejection_reason']);

        return response()->json([
            'message' => 'Job completion rejected.',
            'completion' => [
                'id' => $completion->id,
                'admin_verification_status' => $completion->admin_verification_status,
                'verified_at' => $completion->verified_at,
                'rejection_reason' => $completion->rejection_reason,
            ],
        ]);
    }

    /**
     * Get all jobs (history) for admin – client pays platform, platform pays student.
     */
    public function allJobs(Request $request)
    {
        $query = Job::with(['client', 'invoices.student']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $jobs = $query->latest()->paginate(15)->through(function ($job) {
            $affectedStudents = $job->invoices
                ->pluck('student')
                ->filter()
                ->unique('id')
                ->values()
                ->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'email' => $s->email])
                ->all();

            return [
                'id' => $job->id,
                'title' => $job->title,
                'description' => $job->description,
                'budget' => $job->budget,
                'status' => $job->status,
                'category' => $job->category,
                'start_day' => $job->start_day,
                'end_day' => $job->end_day,
                'workers_needed' => $job->workers_needed,
                'work_type' => $job->work_type,
                'client' => $job->client ? [
                    'id' => $job->client->id,
                    'name' => $job->client->name,
                    'email' => $job->client->email,
                ] : null,
                'affected_students' => $affectedStudents,
                'invoices_count' => $job->invoices->count(),
                'invoices' => $job->invoices->map(fn ($inv) => [
                    'id' => $inv->id,
                    'invoice_number' => $inv->invoice_number,
                    'total_amount' => $inv->total_amount,
                    'commission_amount' => $inv->commission_amount,
                    'student_amount' => $inv->student_amount,
                    'status' => $inv->status,
                    'issued_at' => $inv->issued_at,
                    'paid_at' => $inv->paid_at,
                ]),
                'created_at' => $job->created_at,
                'updated_at' => $job->updated_at,
            ];
        });

        return response()->json($jobs);
    }

    /**
     * Get all invoices for admin – track client payments (platform) and student payouts.
     */
    public function allInvoices(Request $request)
    {
        $query = Invoice::with(['job', 'client', 'student']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest('issued_at')->paginate(15)->through(function ($invoice) {
            return [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'total_amount' => $invoice->total_amount,
                'commission_rate' => $invoice->commission_rate,
                'commission_amount' => $invoice->commission_amount,
                'student_amount' => $invoice->student_amount,
                'status' => $invoice->status,
                'issued_at' => $invoice->issued_at,
                'paid_at' => $invoice->paid_at,
                'job' => $invoice->job ? [
                    'id' => $invoice->job->id,
                    'title' => $invoice->job->title,
                ] : null,
                'client' => $invoice->client ? [
                    'id' => $invoice->client->id,
                    'name' => $invoice->client->name,
                    'email' => $invoice->client->email,
                ] : null,
                'student' => $invoice->student ? [
                    'id' => $invoice->student->id,
                    'name' => $invoice->student->name,
                    'email' => $invoice->student->email,
                ] : null,
            ];
        });

        return response()->json($invoices);
    }

    /**
     * Mark an invoice as paid (client has paid the platform).
     */
    public function markInvoicePaid(Invoice $invoice)
    {
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return response()->json([
            'message' => 'Invoice marked as paid.',
            'invoice' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'paid_at' => $invoice->paid_at,
            ],
        ]);
    }
}