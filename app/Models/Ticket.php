<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'assigned_agent_id',
        'category_id',
        'title',
        'description',
        'priority',
        'status',
    ];

    // =========================================================
    //  CONSTANTS
    //  Mendefinisikan nilai-nilai yang valid untuk status dan priority.
    //  Ini menghindari "magic strings" yang tersebar di seluruh kode.
    // =========================================================

    const STATUSES = ['open', 'in_progress', 'resolved', 'closed'];
    const PRIORITIES = ['low', 'medium', 'high', 'urgent'];

    // =========================================================
    //  MODEL EVENTS — Dijalankan otomatis oleh Eloquent
    // =========================================================

    /**
     * boot() adalah method spesial di Eloquent untuk mendaftarkan
     * "event listener" pada model.
     *
     * 'creating' = event yang dijalankan SEBELUM record baru disimpan ke DB.
     * Di sini kita gunakan untuk generate ticket_number otomatis.
     *
     * Setara dengan Prisma middleware: beforeCreate hook.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Ticket $ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = static::generateTicketNumber();
            }
        });
    }

    /**
     * Generate nomor tiket unik dengan format TK-YYYY-NNNN.
     *
     * Contoh: TK-2026-0001, TK-2026-0042
     *
     * Strategi: ambil angka terakhir dari tiket tahun ini, +1.
     * Wrapped dalam transaction untuk menghindari race condition.
     */
    public static function generateTicketNumber(): string
    {
        $year = now()->year;

        // Hitung tiket yang sudah ada di tahun ini
        // LIKE 'TK-2026-%' untuk filter per tahun
        $lastTicket = static::where('ticket_number', 'like', "TK-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTicket) {
            // Ambil angka di akhir: "TK-2026-0042" → "0042" → 42
            $lastNumber = (int) Str::afterLast($lastTicket->ticket_number, '-');
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        // Format 4 digit dengan leading zeros: 1 → "0001"
        return "TK-{$year}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    // =========================================================
    //  RELATIONSHIPS
    // =========================================================

    /**
     * User yang membuat tiket ini.
     *
     * belongsTo = kebalikan dari hasMany.
     * "Ticket ini MILIK satu User"
     * Di DB: tickets.user_id merujuk ke users.id
     *
     * Cara pakai: $ticket->requester  (mengembalikan User object)
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Agent yang di-assign untuk tiket ini.
     *
     * Juga BelongsTo ke User, tapi via kolom yang berbeda (assigned_agent_id).
     * Karena ada dua relasi ke User, keduanya harus diberi nama berbeda.
     *
     * Cara pakai: $ticket->assignedAgent
     */
    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    /**
     * Kategori tiket ini.
     * Cara pakai: $ticket->category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Semua komentar pada tiket ini.
     * Cara pakai: $ticket->comments  (mengembalikan Collection of Comment)
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'asc');
    }

    /**
     * Semua log aktivitas tiket ini.
     * Cara pakai: $ticket->activityLogs
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->orderBy('created_at', 'asc');
    }

    // =========================================================
    //  HELPERS — Status checks
    // =========================================================

    public function isOpen(): bool       { return $this->status === 'open'; }
    public function isInProgress(): bool { return $this->status === 'in_progress'; }
    public function isResolved(): bool   { return $this->status === 'resolved'; }
    public function isClosed(): bool     { return $this->status === 'closed'; }

    /**
     * Apakah tiket masih "aktif" (bisa diberi komentar/diupdate)?
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['open', 'in_progress']);
    }
}
