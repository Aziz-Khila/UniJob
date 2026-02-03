<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Models\Invoice;
use App\Models\Rating;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ClientController extends Controller
{
    /**
     * Display the client dashboard.
     */
    public function dashboard()
    {
        $jobs = Auth::user()->jobs()->latest()->paginate(10);
        $stats = [
            'total' => Auth::user()->jobs()->count(),
            'open' => Auth::user()->jobs()->where('status', 'open')->count(),
            'in_progress' => Auth::user()->jobs()->where('status', 'in_progress')->count(),
            'completed' => Auth::user()->jobs()->where('status', 'completed')->count(),
        ];

        return response()->json([
            'jobs' => $jobs,
            'stats' => $stats,
        ]);
    }

    /**
     * Display a listing of the client's jobs.
     */
    public function index()
    {
        $jobs = Auth::user()->jobs()->latest()->paginate(10);
        return response()->json($jobs);
    }

    /**
     * Store a newly created job in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'start_day' => ['nullable', 'date'],
            'end_day' => ['nullable', 'date', 'after_or_equal:start_day'],
            'workers_needed' => ['required', 'integer', 'min:1'],
            'work_type' => ['required', 'in:on-site,remote,hybrid'],
        ]);

        $job = Job::create([
            'client_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'] ?? null,
            'budget' => $validated['budget'] ?? null,
            'start_day' => $validated['start_day'] ?? null,
            'end_day' => $validated['end_day'] ?? null,
            'workers_needed' => $validated['workers_needed'],
            'work_type' => $validated['work_type'],
            'status' => 'open',
        ]);

        return response()->json([
            'job' => $job,
            'message' => 'Job posted successfully!',
        ], 201);
    }

    /**
     * Display the specified job.
     */
    public function show(Job $job)
    {
        if ($job->client_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $applications = $job->applications()
            ->with(['student.ratings', 'completion', 'rating'])
            ->get()
            ->map(function ($application) {
                $student = $application->student;
                return [
                    'id' => $application->id,
                    'status' => $application->status,
                    'cover_letter' => $application->cover_letter,
                    'cv_file' => $application->cv_file,
                    'created_at' => $application->created_at,
                    'completion' => $application->completion ? [
                        'id' => $application->completion->id,
                        'admin_verification_status' => $application->completion->admin_verification_status,
                    ] : null,
                    'rating' => $application->rating ? [
                        'id' => $application->rating->id,
                        'rating' => $application->rating->rating,
                        'comment' => $application->rating->comment,
                    ] : null,
                    'student' => [
                        'id' => $student->id,
                        'name' => $student->name,
                        'email' => $student->email,
                        'verification_status' => $student->verification_status,
                        'average_rating' => $student->average_rating,
                        'total_ratings' => $student->total_ratings,
                    ],
                    'average_rating' => $student->average_rating, // For sorting
                ];
            })
            ->sortByDesc('average_rating') // Sort by rating (highest first)
            ->values(); // Reset array keys

        return response()->json([
            'job' => $job,
            'applications' => $applications,
            'accepted_count' => $job->acceptedApplicationsCount(),
        ]);
    }

    /**
     * Update the specified job in storage.
     */
    public function update(Request $request, Job $job)
    {
        if ($job->client_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'start_day' => ['nullable', 'date'],
            'end_day' => ['nullable', 'date', 'after_or_equal:start_day'],
            'status' => ['required', 'in:open,in_progress,completed,cancelled'],
            'workers_needed' => ['required', 'integer', 'min:1'],
            'work_type' => ['required', 'in:on-site,remote,hybrid'],
        ]);

        $job->update($validated);

        return response()->json([
            'job' => $job->fresh(),
            'message' => 'Job updated successfully!',
        ]);
    }

    /**
     * Remove the specified job from storage.
     */
    public function destroy(Job $job)
    {
        if ($job->client_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $job->delete();

        return response()->json(['message' => 'Job deleted successfully!']);
    }

    /**
     * Accept an application.
     */
    public function acceptApplication(Application $application)
    {
        if ($application->job->client_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($application->status !== 'pending') {
            return response()->json(['message' => 'This application cannot be accepted.'], 400);
        }

        $job = $application->job;
        
        // Check if job has reached worker limit
        if ($job->hasReachedWorkerLimit()) {
            return response()->json([
                'message' => 'You have already accepted the maximum number of workers (' . $job->workers_needed . ') for this job.'
            ], 400);
        }

        $application->update(['status' => 'accepted']);

        if ($job->status === 'open') {
            $job->update(['status' => 'in_progress']);
        }

        // Load relationships for notification
        $application->load('student', 'job');

        // Notify student
        NotificationService::applicationAccepted($application->student, $application, $job);

        return response()->json([
            'message' => 'Application accepted successfully!',
            'accepted_count' => $job->fresh()->acceptedApplicationsCount(),
            'workers_needed' => $job->workers_needed,
        ]);
    }

    /**
     * Reject an application.
     */
    public function rejectApplication(Application $application)
    {
        if ($application->job->client_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($application->status !== 'pending') {
            return response()->json(['message' => 'This application cannot be rejected.'], 400);
        }

        $application->update(['status' => 'rejected']);

        // Notify student
        NotificationService::applicationRejected($application->student, $application, $application->job);

        return response()->json(['message' => 'Application rejected.']);
    }

    /**
     * Rate a student after job completion.
     */
    public function rateStudent(Request $request, Application $application)
    {
        if ($application->job->client_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Check if job completion is approved
        if (!$application->completion || $application->completion->admin_verification_status !== 'approved') {
            return response()->json(['message' => 'You can only rate after the job completion is approved.'], 400);
        }

        // Check if already rated
        if ($application->rating) {
            return response()->json(['message' => 'You have already rated this student.'], 400);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $rating = Rating::create([
            'job_id' => $application->job_id,
            'application_id' => $application->id,
            'client_id' => Auth::id(),
            'student_id' => $application->student_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        // Load relationships for notification
        $application->load('job', 'student');

        // Notify student
        NotificationService::newRating($application->student, $rating, $application->job);

        return response()->json([
            'message' => 'Rating submitted successfully!',
            'rating' => $rating,
        ], 201);
    }

    /**
     * Get rating for an application.
     */
    public function getRating(Application $application)
    {
        if ($application->job->client_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $rating = $application->rating;

        return response()->json([
            'rating' => $rating,
        ]);
    }

    /**
     * Display a listing of the client's invoices.
     */
    public function invoices()
    {
        $invoices = Auth::user()->invoicesAsClient()
            ->with(['job', 'student', 'application'])
            ->latest()
            ->paginate(10);

        return response()->json($invoices);
    }

    /**
     * Display the specified invoice.
     */
    public function showInvoice(Invoice $invoice)
    {
        if ($invoice->client_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $invoice->load(['job', 'student', 'application']);

        return response()->json($invoice);
    }

    /**
     * Download the invoice as PDF.
     */
    public function downloadInvoice(Invoice $invoice)
    {
        if ($invoice->client_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $invoice->load(['job', 'student', 'client', 'application']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }
}
