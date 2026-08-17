<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    // ActivityLog tidak menggunakan HasFactory karena tidak perlu factory
    // (log dibuat secara programatik, bukan untuk seeding test data acak)

    protected $fillable = ['ticket_id', 'user_id', 'action', 'description'];

    // ActivityLog hanya punya created_at, tidak ada updated_at
    // Ini memberitahu Eloquent untuk tidak mencoba menulis updated_at
    const UPDATED_AT = null;

    // =========================================================
    //  RELATIONSHIPS
    // =========================================================

    /**
     * Tiket yang berkaitan dengan log ini.
     * Cara pakai: $log->ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * User yang melakukan aksi ini.
     * Nullable karena bisa saja system-generated.
     * Cara pakai: $log->actor
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // =========================================================
    //  STATIC HELPERS — Cara mudah membuat log entry
    //  Dipanggil dari controller: ActivityLog::record($ticket, $user, ...)
    // =========================================================

    /**
     * Buat entry log baru.
     * Memusatkan pembuatan log dalam satu tempat — mudah dipanggil
     * dari controller mana pun tanpa duplikasi kode.
     */
    public static function record(
        Ticket $ticket,
        ?User $user,
        string $action,
        string $description
    ): self {
        return static::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $user?->id, // nullable-safe: jika $user null, hasilnya null
            'action'      => $action,
            'description' => $description,
        ]);
    }
}
