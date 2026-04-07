<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Warz extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\WarzFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'topic',
        'warrior_names',
        'warrior_contacts',
        'prize',
        'user_id',
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

    public function host()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function users(): HasMany
    {
        // return $this->belongsToMany(User::class);
        // return $this->hasMany(Warrior::class, 'user_id', 'id');
        return $this->HasMany(User::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Stories::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(WarzComment::class);
    }
}
