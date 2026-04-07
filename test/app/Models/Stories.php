<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Stories extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\StoryFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'story',
        'user_id',
        'warz_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function war(): HasOne
    {
        return $this->hasOne(Warz::class, 'id', 'warz_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function scopeForWarrior(Builder $query, int $warId, int $userId): Builder
    {
        return $query->where('warz_id', $warId)->where('user_id', $userId);
    }

    public static function countForWarrior(int $warId, int $userId): int
    {
        return static::query()->forWarrior($warId, $userId)->count();
    }

    public static function findWithAuthor(int $storyId): ?self
    {
        return static::query()
            ->select('stories.*', 'users.name', 'users.avatar')
            ->join('users', 'stories.user_id', '=', 'users.id')
            ->where('stories.id', $storyId)
            ->first();
    }
}
