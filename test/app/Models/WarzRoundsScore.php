<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarzRoundsScore extends Model
{
    /** @use HasFactory<\Database\Factories\WarzFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'warz_rounds_id',
        'warz_id',
        'score',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(WarzRound::class, 'warz_rounds_id');
    }

    public function war(): BelongsTo
    {
        return $this->belongsTo(Warz::class, 'warz_id');
    }
}
