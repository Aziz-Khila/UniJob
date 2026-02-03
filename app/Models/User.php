<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'identity_card_number',
        'address',
        'university',
        'last_3_numbers',
        'profile_picture',
        'cv_file',
        'identity_card',
        'student_card',
        'verification_status',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'banned_at',
        'ban_reason',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'banned_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if the user is banned.
     */
    public function isBanned(): bool
    {
        return $this->banned_at !== null;
    }

    /**
     * Get the jobs posted by this client.
     */
    public function jobs()
    {
        return $this->hasMany(Job::class, 'client_id');
    }

    /**
     * Get the applications made by this student.
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'student_id');
    }

    /**
     * Get the admin who verified this user.
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if the user is verified.
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'approved';
    }

    /**
     * Check if the user has pending verification.
     */
    public function isVerificationPending(): bool
    {
        return $this->verification_status === 'pending';
    }

    /**
     * Check if verification was rejected.
     */
    public function isVerificationRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }

    /**
     * Get invoices where this user is the client.
     */
    public function invoicesAsClient()
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }

    /**
     * Get invoices where this user is the student.
     */
    public function invoicesAsStudent()
    {
        return $this->hasMany(Invoice::class, 'student_id');
    }

    /**
     * Get ratings given to this student.
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'student_id');
    }

    /**
     * Get average rating for this student.
     */
    public function getAverageRatingAttribute()
    {
        $average = $this->ratings()->avg('rating');
        return $average ? round($average, 2) : 0;
    }

    /**
     * Get total number of ratings for this student.
     */
    public function getTotalRatingsAttribute()
    {
        return $this->ratings()->count();
    }

    /**
     * Get the notifications for this user.
     */
    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    /**
     * Get unread notifications count.
     */
    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->whereNull('read_at')->count();
    }

    /**
     * Get conversations where this user is user1.
     */
    public function conversationsAsUser1()
    {
        return $this->hasMany(Conversation::class, 'user1_id');
    }

    /**
     * Get conversations where this user is user2.
     */
    public function conversationsAsUser2()
    {
        return $this->hasMany(Conversation::class, 'user2_id');
    }

    /**
     * Get all conversations for this user.
     */
    public function conversations()
    {
        return Conversation::where('user1_id', $this->id)
            ->orWhere('user2_id', $this->id);
    }

    /**
     * Get messages sent by this user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the profile picture URL.
     */
    public function getProfilePictureUrlAttribute()
    {
        if (!$this->profile_picture) {
            return null;
        }

        // If it's already a full URL, return as is
        if (filter_var($this->profile_picture, FILTER_VALIDATE_URL)) {
            return $this->profile_picture;
        }

        // Convert storage path to URL
        return Storage::disk('public')->url($this->profile_picture);
    }

    /**
     * Append the profile picture URL to the model's array representation.
     */
    protected $appends = ['profile_picture_url'];
}
