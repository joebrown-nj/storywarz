<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'avatar',
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
            'password' => 'hashed',
        ];
    }

    public function warz(): BelongsToMany
    {
        return $this->belongsToMany(Warz::class, 'user_warz', 'user_id', 'warz_id');
    }

    public function messageSettings()
    {
        return $this->hasOne(MessageSettings::class);
    }

    public function scopeInWarWithStats(Builder $query, int $warId): Builder
    {
        return $query->select(
            'users.*',
            DB::raw('count(stories.id) as story_count'),
            DB::raw('(SELECT sum(score) from warz_round_scores where warz_round_scores.user_id = users.id and warz_round_scores.warz_id = ' . $warId . ') as score')
        )
            ->join('user_warz', 'users.id', '=', 'user_warz.user_id')
            ->leftJoin('stories', function ($join) {
                $join->on('stories.user_id', '=', 'users.id')
                    ->on('stories.warz_id', '=', 'user_warz.warz_id');
            })
            ->where('user_warz.warz_id', $warId)
            ->groupBy('users.id')
            ->orderBy('score', 'desc');
    }
}
