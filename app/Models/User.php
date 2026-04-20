<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'full_name', 'email', 'role', 'status', 'region', 'specialty', 'bio', 'password', 'otp_code', 'otp_expires_at', 'email_verified_at'])]
#[Hidden(['password', 'remember_token', 'otp_code'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function dashboardRouteName(): string
    {
        return match ($this->role) {
            'guide', 'tour_guide' => 'dashboard.guide',
            'admin' => 'dashboard.admin',
            default => 'dashboard.tourist',
        };
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'created_by');
    }

    public function marketplaceTours(): HasMany
    {
        return $this->hasMany(Tour::class, 'guide_id');
    }

    public function guideAvailabilities(): HasMany
    {
        return $this->hasMany(GuideAvailability::class, 'guide_id');
    }

    public function bookingRequestsAsGuide(): HasMany
    {
        return $this->hasMany(BookingRequest::class, 'guide_id');
    }

    public function bookingRequestsAsTourist(): HasMany
    {
        return $this->hasMany(BookingRequest::class, 'tourist_id');
    }

    public function bookingsAsGuide(): HasMany
    {
        return $this->hasMany(Booking::class, 'guide_id');
    }

    public function bookingsAsTourist(): HasMany
    {
        return $this->hasMany(Booking::class, 'tourist_id');
    }

    public function guideStories(): HasMany
    {
        return $this->hasMany(GuideStory::class, 'guide_id');
    }

    public function guideEarnings(): HasMany
    {
        return $this->hasMany(GuideEarning::class, 'guide_id');
    }

    public function guideReviews(): HasMany
    {
        return $this->hasMany(TourReview::class, 'guide_id');
    }

    public function tourGuideProfile(): HasOne
    {
        return $this->hasOne(TourGuideProfile::class);
    }
}
