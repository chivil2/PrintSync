<?php

namespace App\Models;

use Database\Factories\ConversationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['quote_id', 'customer_id', 'owner_id', 'last_message_at', 'customer_last_read_at', 'owner_last_read_at'])]
class Conversation extends Model
{
    /** @use HasFactory<ConversationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'customer_last_read_at' => 'datetime',
            'owner_last_read_at' => 'datetime',
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function hasParticipant(User $user): bool
    {
        return $user->id === $this->customer_id || $user->id === $this->owner_id;
    }

    public function unreadCountFor(User $user): int
    {
        $lastRead = $user->id === $this->customer_id
            ? $this->customer_last_read_at
            : $this->owner_last_read_at;

        return $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->when($lastRead, fn ($q) => $q->where('created_at', '>', $lastRead))
            ->count();
    }

    public function markReadBy(User $user): void
    {
        $column = $user->id === $this->customer_id
            ? 'customer_last_read_at'
            : 'owner_last_read_at';

        $this->forceFill([$column => now()])->save();
    }
}
