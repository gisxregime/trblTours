<?php

namespace App\Models;

use Database\Factories\TourFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
    /** @use HasFactory<TourFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'guide_id',
        'created_by',
        'name',
        'title',
        'region',
        'city',
        'category',
        'summary',
        'description',
        'duration',
        'duration_label',
        'duration_hours',
        'duration_unit',
        'min_guests',
        'max_guests',
        'difficulty',
        'short_description',
        'full_itinerary',
        'inclusions',
        'custom_inclusions',
        'exclusions',
        'what_to_bring',
        'price',
        'base_price',
        'price_per_person',
        'pricing_tiers',
        'fiesta_surcharge_enabled',
        'fiesta_surcharge_amount',
        'fiesta_start_date',
        'fiesta_end_date',
        'blackout_dates',
        'max_people',
        'image_url',
        'image_path',
        'featured_image',
        'gallery_images',
        'status',
        'admin_approved',
        'activities',
        'is_featured',
        'available_on',
        'rating',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'base_price' => 'decimal:2',
            'price_per_person' => 'decimal:2',
            'duration_hours' => 'integer',
            'min_guests' => 'integer',
            'max_guests' => 'integer',
            'inclusions' => 'array',
            'custom_inclusions' => 'array',
            'pricing_tiers' => 'array',
            'blackout_dates' => 'array',
            'gallery_images' => 'array',
            'fiesta_surcharge_enabled' => 'boolean',
            'fiesta_surcharge_amount' => 'decimal:2',
            'fiesta_start_date' => 'date',
            'fiesta_end_date' => 'date',
            'available_on' => 'date',
            'rating' => 'decimal:1',
            'admin_approved' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function guide(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function marketplaceGuide(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guide_id');
    }

    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tour_likes')->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class);
    }
}
