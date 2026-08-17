<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan kolom 'role' ke tabel users.
     * Kita pakai string biasa (bukan MySQL ENUM) agar lebih fleksibel
     * dan mudah dipahami. Default 'user' karena sebagian besar akun
     * baru adalah requester biasa.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Letakkan setelah kolom 'name' agar urutan kolom rapi di DB
            $table->string('role')->default('user')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Method down() adalah kebalikan dari up() — digunakan saat rollback.
     * Setara dengan "undo" migrasi ini.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
