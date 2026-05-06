<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuideAvailability extends Model
{
    protected $table = 'guide_availability';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'guide_id',
        'date',
        'status',
        'slots',
        'note',
        'special_price',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'slots' => 'integer',
            'special_price' => 'decimal:2',
        ];
    }

    public function guide(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guide_id');
    }
}
