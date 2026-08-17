<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Fillable = kolom yang boleh di-mass assign (setara dengan whitelist)
// Mass assignment: User::create(['name' => ..., 'role' => ...])
#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // =========================================================
    //  ROLE HELPERS
    //  Method-method kecil ini membuat pengecekan role lebih
    //  readable di controller dan Blade: $user->isAdmin()
    //  daripada $user->role === 'admin'
    // =========================================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    // =========================================================
    //  RELATIONSHIPS
    //  Hubungan antar model — ini inti dari Eloquent ORM
    // =========================================================

    /**
     * Tiket-tiket yang DIBUAT oleh user ini.
     *
     * hasMany = "User ini punya banyak Ticket"
     * Di DB: tickets.user_id merujuk ke users.id
     *
     * Cara pakai: $user->tickets   (mengembalikan collection of Ticket)
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    /**
     * Tiket-tiket yang di-ASSIGN ke agent ini.
     *
     * Foreign key berbeda — harus eksplisit disebutkan
     * Cara pakai: $agent->assignedTickets
     */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_agent_id');
    }

    /**
     * Komentar-komentar yang dibuat user ini.
     * Cara pakai: $user->comments
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Log aktivitas yang dilakukan user ini.
     * Cara pakai: $user->activityLogs
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // =========================================================
    //  CASTS
    //  Konversi otomatis tipe data saat baca/tulis dari DB
    // =========================================================

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed', // password otomatis di-hash saat di-set
        ];
    }
}
