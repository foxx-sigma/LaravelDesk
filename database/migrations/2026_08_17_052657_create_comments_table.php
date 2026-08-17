<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel comments — percakapan antara user dan agent dalam satu tiket.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            // Tiket mana yang dikomentari
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');

            // Siapa yang berkomentar
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->text('body'); // Isi komentar

            $table->timestamps();

            // Index untuk query "ambil semua komentar tiket X"
            $table->index('ticket_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
