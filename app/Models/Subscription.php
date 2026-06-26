<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'channel',
        'region',
        'categories',
        'contact',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'categories' => 'array',
            'status' => SubmissionStatus::class,
        ];
    }
}
