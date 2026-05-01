<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TouristRequest extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'tour_listing_id',
        'tourist_id',
        'selected_guide_id',
        'selected_comment_id',
        'service_location_id',
        'title',
        'location',
        'preferred_date',
        'passenger_count',
        'budget_min',
        'budget_max',
        'duration',
        'region',
        'adults',
        'children',
        'interests',
        'description',
        'message',
        'status',
        'completed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function tourListing(): BelongsTo
    {
        return $this->belongsTo(TourListing::class);
    }

    public function tourist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tourist_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function selectedGuide(): BelongsTo
    {
        return $this->belongsTo(User::class, 'selected_guide_id');
    }

    public function selectedComment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'selected_comment_id');
    }

    public function booking(): HasOne
    {
        return $this->hasOne(Booking::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}
