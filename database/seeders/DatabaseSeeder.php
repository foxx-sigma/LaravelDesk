<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Seeder adalah script yang mengisi database dengan data awal.
     * Dijalankan dengan: php artisan db:seed
     * Atau bersamaan migrate: php artisan migrate --seed
     */
    public function run(): void
    {
        // =====================================================
        // 1. AKUN TETAP — Untuk development dan demo
        //    Kredensial ini HARUS didokumentasikan di README
        //    dan TIDAK BOLEH digunakan di production
        // =====================================================

        $admin = User::create([
            'name'              => 'Admin LaravelDesk',
            'email'             => 'admin@laraveldesk.test',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        $agent1 = User::create([
            'name'              => 'Sarah Agent',
            'email'             => 'agent@laraveldesk.test',
            'password'          => Hash::make('password'),
            'role'              => 'agent',
            'email_verified_at' => now(),
        ]);

        $agent2 = User::create([
            'name'              => 'Budi Agent',
            'email'             => 'agent2@laraveldesk.test',
            'password'          => Hash::make('password'),
            'role'              => 'agent',
            'email_verified_at' => now(),
        ]);

        $user1 = User::create([
            'name'              => 'John User',
            'email'             => 'user@laraveldesk.test',
            'password'          => Hash::make('password'),
            'role'              => 'user',
            'email_verified_at' => now(),
        ]);

        // =====================================================
        // 2. USER ACAK TAMBAHAN — Agar data lebih realistis
        // =====================================================

        $extraUsers = User::factory(8)->create(); // 8 user biasa tambahan
        $extraAgents = User::factory(2)->agent()->create(); // 2 agent tambahan
        $allUsers = $extraUsers->merge([$user1]);
        $allAgents = collect([$agent1, $agent2])->merge($extraAgents);

        // =====================================================
        // 3. CATEGORIES — 6 kategori standar helpdesk
        // =====================================================

        $categories = collect([
            ['name' => 'Hardware',  'description' => 'Masalah perangkat keras seperti komputer, monitor, keyboard, mouse, dll.'],
            ['name' => 'Software',  'description' => 'Masalah aplikasi, instalasi, lisensi, atau bug software.'],
            ['name' => 'Network',   'description' => 'Masalah koneksi internet, WiFi, VPN, atau jaringan internal.'],
            ['name' => 'Account',   'description' => 'Masalah akun, password, atau akses sistem.'],
            ['name' => 'Access',    'description' => 'Permintaan akses ke sistem, folder, atau resource perusahaan.'],
            ['name' => 'Other',     'description' => 'Permintaan atau masalah lain yang tidak termasuk kategori di atas.'],
        ])->map(fn ($cat) => Category::create($cat));

        // =====================================================
        // 4. TICKETS — Berbagai status dan prioritas
        // =====================================================

        // Tiket OPEN — belum di-assign
        $openTickets = collect();
        for ($i = 0; $i < 5; $i++) {
            $ticket = Ticket::factory()->open()->create([
                'user_id'     => $allUsers->random()->id,
                'category_id' => $categories->random()->id,
            ]);
            ActivityLog::record($ticket, null, 'ticket_created',
                "Tiket {$ticket->ticket_number} dibuat oleh {$ticket->requester->name}."
            );
            $openTickets->push($ticket);
        }

        // Tiket IN_PROGRESS — sudah di-assign ke agent
        for ($i = 0; $i < 6; $i++) {
            $agent = $allAgents->random();
            $ticket = Ticket::factory()->create([
                'user_id'           => $allUsers->random()->id,
                'assigned_agent_id' => $agent->id,
                'category_id'       => $categories->random()->id,
                'status'            => 'in_progress',
                'priority'          => collect(['medium', 'high', 'urgent'])->random(),
            ]);
            ActivityLog::record($ticket, null, 'ticket_created',
                "Tiket {$ticket->ticket_number} dibuat."
            );
            ActivityLog::record($ticket, $admin, 'agent_assigned',
                "{$admin->name} menugaskan tiket ke {$agent->name}."
            );
            ActivityLog::record($ticket, $agent, 'status_changed',
                "{$agent->name} mengubah status menjadi In Progress."
            );

            // Tambahkan beberapa komentar
            Comment::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $ticket->user_id,
                'body'      => 'Terima kasih sudah menangani tiket saya. Apakah ada update terbaru?',
            ]);
            Comment::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $agent->id,
                'body'      => 'Sedang dalam proses penanganan. Kami akan segera memberikan update.',
            ]);
        }

        // Tiket RESOLVED
        for ($i = 0; $i < 4; $i++) {
            $agent = $allAgents->random();
            $requester = $allUsers->random();
            $ticket = Ticket::factory()->resolved()->create([
                'user_id'           => $requester->id,
                'assigned_agent_id' => $agent->id,
                'category_id'       => $categories->random()->id,
            ]);
            ActivityLog::record($ticket, $agent, 'status_changed',
                "{$agent->name} menandai tiket sebagai Resolved."
            );
            Comment::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $agent->id,
                'body'      => 'Masalah telah diselesaikan. Silakan konfirmasi jika sudah beres.',
            ]);
        }

        // Tiket CLOSED
        for ($i = 0; $i < 3; $i++) {
            $requester = $allUsers->random();
            $ticket = Ticket::factory()->closed()->create([
                'user_id'           => $requester->id,
                'assigned_agent_id' => $allAgents->random()->id,
                'category_id'       => $categories->random()->id,
            ]);
            ActivityLog::record($ticket, $requester, 'ticket_closed',
                "{$requester->name} menutup tiket."
            );
        }

        // Tiket user1 khusus — agar demo lebih mudah
        $demoTicket = Ticket::factory()->open()->create([
            'user_id'     => $user1->id,
            'category_id' => $categories->firstWhere('name', 'Software')->id,
            'title'       => 'Aplikasi Microsoft Teams tidak bisa dibuka',
            'description' => 'Setelah update Windows kemarin, Microsoft Teams tidak bisa dibuka. Muncul error "Teams.exe has stopped working". Sudah dicoba restart tapi masih sama.',
            'priority'    => 'high',
        ]);
        ActivityLog::record($demoTicket, $user1, 'ticket_created',
            "{$user1->name} membuat tiket {$demoTicket->ticket_number}."
        );

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Demo accounts (password: password):');
        $this->command->info('  Admin : admin@laraveldesk.test');
        $this->command->info('  Agent : agent@laraveldesk.test');
        $this->command->info('  User  : user@laraveldesk.test');
    }
}
