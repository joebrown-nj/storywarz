<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MessageSettings extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\MessageSettingsFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'send_sms_notifications',
        'send_email_notifications'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the user that owns the message settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messageSettings()
    {
        return $this->hasOne(MessageSettings::class);
    }
}
