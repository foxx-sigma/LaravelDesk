<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel activity_logs — rekaman ringkas semua kejadian pada tiket.
     * Ini bukan event sourcing — hanya catatan human-readable untuk audit trail.
     *
     * Contoh entry:
     *   action: 'ticket_created'
     *   description: 'John membuat tiket TK-2026-0001'
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Tiket yang terkait dengan aktivitas ini
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');

            // Siapa yang melakukan — nullable karena bisa saja system-generated
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Jenis aksi — untuk filtering jika diperlukan
            // Contoh: 'ticket_created', 'status_changed', 'agent_assigned', 'comment_added'
            $table->string('action');

            // Deskripsi human-readable yang akan ditampilkan di UI
            $table->text('description');

            // Hanya created_at — log tidak pernah di-update
            $table->timestamp('created_at')->useCurrent();

            // Index untuk query "ambil semua log tiket X, urut terbaru"
            $table->index(['ticket_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
