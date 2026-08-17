<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel categories menyimpan kategori tiket (Hardware, Software, dll).
     * Admin dapat mengelola categories; tickets menggunakan foreign key ke sini.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();                            // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('name')->unique();        // Nama kategori, harus unik
            $table->string('description')->nullable(); // Deskripsi opsional
            $table->timestamps();                    // created_at dan updated_at otomatis
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
