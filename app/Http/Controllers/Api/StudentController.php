<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\JobCompletion;
use App\Models\Rating;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function dashboard()
    {
        $applications = Auth::user()->applications()->with('job')->latest()->paginate(10);
        $stats = [
            'total' => Auth::user()->applications()->count(),
            'pending' => Auth::user()->applications()->where('status', 'pending')->count(),
            'accepted' => Auth::user()->applications()->where('status', 'accepted')->count(),
            'rejected' => Auth::user()->applications()->where('status', 'rejected')->count(),
        ];

        return response()->json([
            'applications' => $applications,
            'stats' => $stats,
        ]);
    }

    /**
     * Display a listing of available jobs.
     */
    public function browseJobs(Request $request)
    {
        $query = Job::whereIn('status', ['open', 'in_progress'])
            ->with('client')
            // Exclude jobs where the current student has already applied
            ->whereDoesntHave('applications', function($q) {
                $q->where('student_id', Auth::id());
            })
            ->where(function($q) {
                // Show jobs that either:
                // 1. Don't have a workers_needed limit (workers_needed IS NULL)
                // 2. Have a limit but haven't reached it yet (accepted_count < workers_needed)
                $q->whereNull('workers_needed')
                  ->orWhere(function($subQ) {
                      // For jobs with workers_needed set, check if accepted count is less than needed
                      $subQ->whereNotNull('workers_needed')
                           ->whereRaw('(SELECT COUNT(*) FROM job_applications WHERE job_applications.job_id = job_postings.id AND job_applications.status = ?) < job_postings.workers_needed', ['accepted']);
                  });
            })
            ->latest();
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        $jobs = $query->withCount(['applications as accepted_count' => function($q) {
            $q->where('status', 'accepted');
        }])->paginate(12);
        
        $categories = Job::whereIn('status', ['open', 'in_progress'])->whereNotNull('category')->distinct()->pluck('category');

        return response()->json([
            'jobs' => $jobs,
            'categories' => $categories,
        ]);
    }

    /**
     * Display the specified job.
     */
    public function showJob(Job $job)
    {
        // Check if job has reached worker limit
        $acceptedCount = $job->applications()->where('status', 'accepted')->count();
        if ($job->workers_needed && $acceptedCount >= $job->workers_needed) {
            return response()->json([
                'message' => 'This job is no longer accepting applications as all required positions have been filled.',
            ], 404);
        }

        $hasApplied = Auth::user()->applications()->where('job_id', $job->id)->exists();
        $application = Auth::user()->applications()->where('job_id', $job->id)->first();

        return response()->json([
            'job' => $job->load('client'),
            'hasApplied' => $hasApplied,
            'application' => $application,
        ]);
    }

    /**
     * Store a new application.
     */
    public function storeApplication(Request $request, Job $job)
    {
        if (Auth::user()->applications()->where('job_id', $job->id)->exists()) {
            return response()->json(['message' => 'You have already applied to this job.'], 400);
        }

        // Allow applying to 'open' or 'in_progress' jobs
        if (!in_array($job->status, ['open', 'in_progress'])) {
            return response()->json(['message' => 'This job is no longer accepting applications.'], 400);
        }

        // Check if job has reached worker limit
        $acceptedCount = $job->applications()->where('status', 'accepted')->count();
        if ($job->workers_needed && $acceptedCount >= $job->workers_needed) {
            return response()->json(['message' => 'This job is no longer accepting applications as all required positions have been filled.'], 400);
        }

        $user = Auth::user();
        if (empty($user->cv_file)) {
            return response()->json(['message' => 'Please upload your CV in your profile before applying.'], 400);
        }

        $validated = $request->validate([
            'cover_letter' => ['nullable', 'string', 'max:2000'],
        ]);

        $cvPath = 'application_cvs/' . time() . '_' . basename($user->cv_file);
        Storage::disk('public')->copy($user->cv_file, $cvPath);

        $application = Application::create([
            'student_id' => Auth::id(),
            'job_id' => $job->id,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'cv_file' => $cvPath,
            'status' => 'pending',
        ]);

        // Load relationships for notification
        $application->load('student', 'job.client');

        // Notify client
        NotificationService::newApplication($job->client, $application, $job);

        return response()->json([
            'application' => $application,
            'message' => 'Application submitted successfully!',
        ], 201);
    }

    /**
     * Display the student's applications.
     */
    public function myApplications()
    {
        $applications = Auth::user()->applications()
            ->with(['job.client', 'completion'])
            ->latest()
            ->paginate(10);

        // Add completion file URL if exists
        $applications->getCollection()->transform(function ($application) {
            if ($application->completion) {
                $application->completion->completion_file_url = Storage::url($application->completion->completion_file);
            }
            return $application;
        });

        return response()->json($applications);
    }

    /**
     * Withdraw an application.
     */
    public function withdrawApplication(Application $application)
    {
        if ($application->student_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($application->status !== 'pending') {
            return response()->json(['message' => 'You can only withdraw pending applications.'], 400);
        }

        $application->update(['status' => 'withdrawn']);

        return response()->json(['message' => 'Application withdrawn successfully.']);
    }

    /**
     * Display the student's profile.
     */
    public function profile()
    {
        $user = Auth::user();
        $userData = $user->toArray();
        $userData['average_rating'] = $user->average_rating;
        $userData['total_ratings'] = $user->total_ratings;
        
        // Convert profile_picture path to URL if it exists
        if (!empty($userData['profile_picture']) && !filter_var($userData['profile_picture'], FILTER_VALIDATE_URL)) {
            $userData['profile_picture'] = Storage::disk('public')->url($userData['profile_picture']);
        }
        
        return response()->json($userData);
    }

    /**
     * Get all ratings for the authenticated student.
     */
    public function ratings()
    {
        $ratings = Auth::user()->ratings()
            ->with(['job', 'client', 'application'])
            ->latest()
            ->get()
            ->map(function ($rating) {
                return [
                    'id' => $rating->id,
                    'rating' => $rating->rating,
                    'comment' => $rating->comment,
                    'created_at' => $rating->created_at,
                    'job' => [
                        'id' => $rating->job->id,
                        'title' => $rating->job->title,
                    ],
                    'client' => [
                        'id' => $rating->client->id,
                        'name' => $rating->client->name,
                        'email' => $rating->client->email,
                    ],
                ];
            });

        return response()->json($ratings);
    }

    /**
     * Update the student's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'address' => ['nullable', 'string'],
                'university' => ['nullable', 'string', 'max:255'],
                'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                'cv_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
                'identity_card' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpeg,png,jpg', 'max:5120'],
                'student_card' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpeg,png,jpg', 'max:5120'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Profile update validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'user_id' => $user->id,
            ]);
            throw $e;
        }

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = $path;
        } else {
            unset($validated['profile_picture']);
        }

        if ($request->hasFile('cv_file')) {
            if ($user->cv_file && Storage::disk('public')->exists($user->cv_file)) {
                Storage::disk('public')->delete($user->cv_file);
            }
            $path = $request->file('cv_file')->store('cvs', 'public');
            $validated['cv_file'] = $path;
        } else {
            unset($validated['cv_file']);
        }

        $documentUploaded = false;

        if ($request->hasFile('identity_card')) {
            if ($user->identity_card && Storage::disk('public')->exists($user->identity_card)) {
                Storage::disk('public')->delete($user->identity_card);
            }
            $path = $request->file('identity_card')->store('identity_cards', 'public');
            $validated['identity_card'] = $path;
            $documentUploaded = true;
        } else {
            unset($validated['identity_card']);
        }

        if ($request->hasFile('student_card')) {
            if ($user->student_card && Storage::disk('public')->exists($user->student_card)) {
                Storage::disk('public')->delete($user->student_card);
            }
            $path = $request->file('student_card')->store('student_cards', 'public');
            $validated['student_card'] = $path;
            $documentUploaded = true;
        } else {
            unset($validated['student_card']);
        }

        // Reset verification status to pending if documents were uploaded
        if ($documentUploaded) {
            $validated['verification_status'] = 'pending';
            $validated['verified_at'] = null;
            $validated['verified_by'] = null;
            $validated['rejection_reason'] = null; // Clear rejection reason when resubmitting
        }

        $user->update($validated);
        $user = $user->fresh();
        
        $userData = $user->toArray();
        $userData['average_rating'] = $user->average_rating;
        $userData['total_ratings'] = $user->total_ratings;
        
        // Convert profile_picture path to URL if it exists
        if (!empty($userData['profile_picture']) && !filter_var($userData['profile_picture'], FILTER_VALIDATE_URL)) {
            $userData['profile_picture'] = Storage::disk('public')->url($userData['profile_picture']);
        }

        return response()->json([
            'user' => $userData,
            'message' => 'Profile updated successfully!',
        ]);
    }

    /**
     * Submit job completion with attachment.
     */
    public function submitCompletion(Request $request, Application $application)
    {
        // Ensure the application belongs to the authenticated student
        if ($application->student_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Only allow submitting completion for accepted applications
        if ($application->status !== 'accepted') {
            return response()->json(['message' => 'You can only submit completion for accepted applications.'], 400);
        }

        // Check if completion already exists
        if ($application->completion) {
            return response()->json(['message' => 'Completion already submitted for this application.'], 400);
        }

        $validated = $request->validate([
            'completion_file' => ['required', 'file', 'mimes:pdf,doc,docx,jpeg,png,jpg,zip,rar', 'max:10240'],
        ]);

        // Store the completion file
        $filePath = $request->file('completion_file')->store('job_completions', 'public');

        // Create completion record
        $completion = JobCompletion::create([
            'application_id' => $application->id,
            'completion_file' => $filePath,
            'admin_verification_status' => 'pending',
        ]);

        return response()->json([
            'completion' => [
                'id' => $completion->id,
                'application_id' => $completion->application_id,
                'completion_file_url' => Storage::url($completion->completion_file),
                'admin_verification_status' => $completion->admin_verification_status,
                'created_at' => $completion->created_at,
            ],
            'message' => 'Job completion submitted successfully! Waiting for admin verification.',
        ], 201);
    }

    /**
     * Display a listing of the student's invoices.
     */
    public function invoices()
    {
        $invoices = Auth::user()->invoicesAsStudent()
            ->with(['job', 'client', 'application'])
            ->latest()
            ->paginate(15);

        return response()->json($invoices);
    }

    /**
     * Display the specified invoice (student must be the invoice's student).
     */
    public function showInvoice(Invoice $invoice)
    {
        if ($invoice->student_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $invoice->load(['job', 'client', 'application']);

        return response()->json($invoice);
    }

    /**
     * Download the invoice as PDF (student version with full details highlighted).
     */
    public function downloadInvoice(Invoice $invoice)
    {
        if ($invoice->student_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $invoice->load(['job', 'student', 'client', 'application']);
        $forStudent = true;
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'forStudent'));

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }
}
