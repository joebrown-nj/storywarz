<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class UserWarz extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserWarzFactory> */
    use HasFactory, Notifiable;
    protected $table = 'user_warz';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'warz_id',
        'user_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function scopeForDashboard(Builder $query, int $userId): Builder
    {
        return $query->select('user_warz.*', 'warzs.*', 'users.name as host_name')
            ->addSelect(DB::raw('(SELECT count(id) from warz_rounds where warz_rounds.warz_id = user_warz.warz_id) as round_count'))
            ->where('user_warz.user_id', $userId)
            ->join('warzs', 'user_warz.warz_id', '=', 'warzs.id')
            ->join('users', 'users.id', '=', 'warzs.user_id');
    }

    public function scopeStoryDashboard(Builder $query, int $userId): Builder
    {
        return $query->select('warzs.id', 'warzs.topic', 'warzs.status', 'users.name as host_name')
            ->addSelect(DB::raw('(SELECT count(id) from stories where stories.warz_id = user_warz.warz_id and stories.user_id = user_warz.user_id) as story_count'))
            ->join('warzs', 'warzs.id', '=', 'user_warz.warz_id')
            ->join('users', 'warzs.user_id', '=', 'users.id')
            ->where('user_warz.user_id', $userId);
    }

    public function scopeAccessibleWar(Builder $query, int $userId, int $warId): Builder
    {
        return $query->where('user_warz.user_id', $userId)
            ->where('user_warz.warz_id', $warId)
            ->join('warzs', 'warzs.id', '=', 'user_warz.warz_id');
    }

    public static function findAccessibleWar(int $userId, int $warId): ?object
    {
        return static::query()
            ->select('warzs.*', 'users.name as host_name')
            ->accessibleWar($userId, $warId)
            ->join('users', 'warzs.user_id', '=', 'users.id')
            ->first();
    }

    public static function hasWarAccess(int $userId, int $warId): bool
    {
        return static::query()
            ->accessibleWar($userId, $warId)
            ->exists();
    }
}
