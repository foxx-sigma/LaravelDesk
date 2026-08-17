<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            // ticket_number akan di-generate otomatis oleh model event boot()
            // sehingga tidak perlu didefinisikan di sini
            'user_id'           => User::factory(),      // buat user baru jika tidak disediakan
            'assigned_agent_id' => null,                 // default belum di-assign
            'category_id'       => Category::factory(),  // buat category baru jika tidak ada
            'title'             => fake()->sentence(6),
            'description'       => fake()->paragraphs(2, true),
            'priority'          => fake()->randomElement(Ticket::PRIORITIES),
            'status'            => 'open',
        ];
    }

    // State untuk setiap status
    public function open(): static
    {
        return $this->state(['status' => 'open']);
    }

    public function inProgress(): static
    {
        return $this->state(['status' => 'in_progress']);
    }

    public function resolved(): static
    {
        return $this->state(['status' => 'resolved']);
    }

    public function closed(): static
    {
        return $this->state(['status' => 'closed']);
    }

    // State untuk tiket yang sudah di-assign
    public function assignedTo(User $agent): static
    {
        return $this->state(['assigned_agent_id' => $agent->id, 'status' => 'in_progress']);
    }
}
