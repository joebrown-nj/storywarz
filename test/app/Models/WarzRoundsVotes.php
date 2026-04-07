<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class WarzRoundsVotes extends Model
{
    /** @use HasFactory<\Database\Factories\WarzFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'warz_rounds_id',
        'user_id',
        'warz_id',
        'voted_for_user_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function round(): BelongsTo
    {
        return $this->belongsTo(WarzRounds::class, 'warz_rounds_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function war(): BelongsTo
    {
        return $this->belongsTo(Warz::class, 'warz_id');
    }

    public function votedFor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voted_for_user_id');
    }

    public function scopeWithVoteDetails(Builder $query): Builder
    {
        return $query->select('warz_rounds_votes.*')
            ->addSelect('vu.name as voted_for_name')
            ->addSelect('vu.avatar as voted_for_avatar')
            ->addSelect('u.name as name')
            ->addSelect('u.avatar as avatar')
            ->addSelect(DB::raw('(SELECT score from warz_rounds_scores where warz_rounds_scores.warz_rounds_id = warz_rounds_votes.warz_rounds_id and warz_rounds_scores.user_id = warz_rounds_votes.user_id) as score'))
            ->join('users as u', 'u.id', '=', 'warz_rounds_votes.user_id')
            ->join('users as vu', 'vu.id', '=', 'warz_rounds_votes.voted_for_user_id');
    }

    public function scopeForRound(Builder $query, int $warRoundId): Builder
    {
        return $query->where('warz_rounds_votes.warz_rounds_id', $warRoundId);
    }

    public static function findUserVoteForRound(int $userId, int $warRoundId): ?self
    {
        return static::query()
            ->withVoteDetails()
            ->where('warz_rounds_votes.user_id', $userId)
            ->forRound($warRoundId)
            ->first();
    }
}
