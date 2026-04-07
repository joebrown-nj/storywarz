<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarzRound extends Model
{
    /** @use HasFactory<\Database\Factories\WarzFactory> */
    use HasFactory;

    protected $table = 'warz_rounds';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'warz_id',
        'stories_id',
        'complete',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function war(): BelongsTo
    {
        return $this->belongsTo(Warz::class, 'warz_id');
    }

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class, 'stories_id');
    }

    public function scopeCurrentWithStory(Builder $query, int $warId): Builder
    {
        return $query->select('warz_rounds.*', 'warz_rounds.id as round_id', 'warz_rounds.id as warz_rounds_id', 'stories.*')
            ->join('stories', 'stories.id', '=', 'warz_rounds.stories_id')
            ->where('warz_rounds.warz_id', $warId)
            ->where('warz_rounds.complete', false)
            ->orderBy('warz_rounds.id', 'asc');
    }

    public function scopeLatestCompleted(Builder $query, int $warId): Builder
    {
        return $query->where('warz_id', $warId)
            ->where('complete', true)
            ->orderBy('id', 'desc');
    }

    public static function storyNumber(int $warId): int
    {
        return static::where('warz_id', $warId)->count();
    }
}
