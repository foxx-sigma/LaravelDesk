<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // 'sometimes' = hanya validasi jika field tersebut dikirim dalam request
            // Ini berguna untuk PATCH request di mana tidak semua field dikirim
            'status'            => ['sometimes', 'string', 'in:' . implode(',', Ticket::STATUSES)],
            'priority'          => ['sometimes', 'string', 'in:' . implode(',', Ticket::PRIORITIES)],
            'assigned_agent_id' => ['sometimes', 'nullable', 'exists:users,id'],
            'category_id'       => ['sometimes', 'integer', 'exists:categories,id'],
            'title'             => ['sometimes', 'string', 'max:255'],
            'description'       => ['sometimes', 'string', 'max:5000'],
        ];
    }
}
