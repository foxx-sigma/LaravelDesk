<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreTicketRequest — validasi saat user membuat tiket baru.
 *
 * Konsep Form Request di Laravel:
 * - Setara dengan DTO + ValidationPipe di NestJS
 * - Validasi dipisahkan dari controller agar controller tetap bersih
 * - Laravel otomatis menjalankan validasi ini SEBELUM masuk ke controller method
 * - Jika gagal, Laravel otomatis redirect kembali dengan error messages
 */
class StoreTicketRequest extends FormRequest
{
    /**
     * Apakah user berhak membuat request ini?
     * Karena semua user yang login boleh membuat tiket, return true.
     * Otorisasi yang lebih spesifik ditangani di Policy.
     */
    public function authorize(): bool
    {
        return auth()->check(); // hanya user yang sudah login
    }

    /**
     * Aturan validasi — format sama dengan Laravel validation rules.
     * 'required' = wajib ada
     * 'string'   = harus string
     * 'max:255'  = maksimal 255 karakter
     * 'in:...'   = harus salah satu dari nilai yang diberikan
     * 'exists:...' = harus ada di tabel tersebut di DB
     */
    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'priority'    => ['required', 'string', 'in:' . implode(',', Ticket::PRIORITIES)],
        ];
    }

    /**
     * Pesan error kustom — opsional, tapi membuat UX lebih baik.
     */
    public function messages(): array
    {
        return [
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'priority.in'        => 'Prioritas yang dipilih tidak valid.',
        ];
    }
}
