<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id', 'user_id', 'body'];

    // =========================================================
    //  RELATIONSHIPS
    // =========================================================

    /**
     * Tiket yang berisi komentar ini.
     * Cara pakai: $comment->ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * User yang menulis komentar ini.
     * Cara pakai: $comment->author
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
