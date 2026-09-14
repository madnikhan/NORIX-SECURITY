<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'candidate_id',
    'job_posting_id',
    'reference',
    'sia_licence_number',
    'sia_expiry',
    'right_to_work_share_code',
    'status',
    'notes',
    'status_history',
])]
class Application extends Model
{
    protected function casts(): array
    {
        return [
            'sia_expiry' => 'date',
            'status_history' => 'array',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }
}
