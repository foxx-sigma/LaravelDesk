<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // =========================================================
    //  RELATIONSHIPS
    // =========================================================

    /**
     * Tiket-tiket dalam kategori ini.
     *
     * hasMany = "Category ini punya banyak Ticket"
     * Di DB: tickets.category_id merujuk ke categories.id
     *
     * Cara pakai: $category->tickets
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    /**
     * Cek apakah kategori aman dihapus (tidak punya tiket aktif).
     * Digunakan di controller sebelum menghapus kategori.
     */
    public function hasTickets(): bool
    {
        return $this->tickets()->exists();
    }
}
