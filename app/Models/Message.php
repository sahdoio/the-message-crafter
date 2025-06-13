<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Message extends Model
{
    protected $table = 'messages';

    protected $fillable = [
        'contact_id',
        'provider',          // 'flow' or 'ai'
        'channel',           // 'whatsapp' or 'email'
        'message_type',      // 'text', 'template', 'html', etc.
        'image_url',         //  URL of the image sent (if applicable)
        'payload',           //  message content (array or json)
        'status',            // 'pending', 'sent', 'failed'
        'sent_at',           //  datetime the message was sent
    ];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
    ];

    /**
     * The recipient contact (email or phone).
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * If the message is related to another model (optional).
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Check if the message has already been sent.
     */
    protected function isSent(): Attribute
    {
        return Attribute::get(fn () => !is_null($this->sent_at));
    }
}
