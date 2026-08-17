<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel utama aplikasi. Setiap tiket memiliki:
     * - Satu requester (user_id)
     * - Satu assigned agent (assigned_agent_id) — nullable
     * - Satu category
     * - Nomor tiket unik yang mudah dibaca (TK-2026-0001)
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            // Nomor tiket yang readable — dibuat otomatis di Model
            $table->string('ticket_number')->unique();

            // Siapa yang membuat tiket — foreign key ke tabel users
            // onDelete('cascade'): jika user dihapus, tiket-tiketnya ikut terhapus
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Agent yang di-assign — nullable karena tiket baru belum tentu punya agent
            // nullOnDelete: jika agent dihapus, kolom ini jadi NULL (tiket tetap ada)
            $table->foreignId('assigned_agent_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Kategori tiket — restrict: tidak bisa hapus category yang masih punya ticket
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();

            $table->string('title');
            $table->text('description');

            // Priority dan status sebagai string — lebih mudah dipahami dari ENUM MySQL
            // Nilai yang valid akan kita validasi di Form Request
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('status')->default('open');     // open, in_progress, resolved, closed

            $table->timestamps();

            // Index untuk query yang sering digunakan (filtering di ticket list)
            $table->index('status');
            $table->index('priority');
            $table->index('user_id');
            $table->index('assigned_agent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
