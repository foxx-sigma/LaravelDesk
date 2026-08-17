<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * TicketController — mengelola CRUD tiket.
 *
 * Ini adalah "Resource Controller" — Laravel punya konvensi 7 method standar:
 *   index   → GET  /tickets          → daftar tiket
 *   create  → GET  /tickets/create   → form buat tiket baru
 *   store   → POST /tickets          → simpan tiket baru
 *   show    → GET  /tickets/{ticket} → detail tiket
 *   edit    → GET  /tickets/{id}/edit → form edit
 *   update  → PUT/PATCH /tickets/{id} → simpan edit
 *   destroy → DELETE /tickets/{id}    → hapus tiket
 *
 * Setara dengan NestJS Controller dengan @Get(), @Post(), @Patch(), @Delete()
 */
class TicketController extends Controller
{
    /**
     * Daftar tiket — berbeda berdasarkan role.
     *
     * Konsep penting: FILTERING DI DATABASE, bukan di PHP.
     * Kita tidak ambil semua tiket lalu filter, tapi filter langsung di query.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Mulai query — sesuaikan scope berdasarkan role
        $query = match (true) {
            $user->isAdmin() => Ticket::query(),                              // admin lihat semua
            $user->isAgent() => Ticket::where('assigned_agent_id', $user->id), // agent lihat yang di-assign
            default          => Ticket::where('user_id', $user->id),           // user lihat punyanya sendiri
        };

        // --- FILTERING ---
        // Filter berdasarkan status jika ada di URL: ?status=open
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan priority: ?priority=high
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter berdasarkan category: ?category_id=1
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // --- SEARCH ---
        // Cari berdasarkan nomor tiket atau judul: ?search=TK-2026
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        // --- EAGER LOADING + PAGINATION ---
        // with() = load relasi sekaligus dalam satu query (hindari N+1)
        // paginate() = otomatis bagi hasil menjadi halaman-halaman
        $tickets = $query
            ->with(['requester', 'category', 'assignedAgent'])
            ->latest()
            ->paginate(15)
            ->withQueryString(); // pertahankan filter di URL pagination

        $categories = Category::orderBy('name')->get();

        return view('tickets.index', compact('tickets', 'categories'));
    }

    /**
     * Form membuat tiket baru.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('tickets.create', compact('categories'));
    }

    /**
     * Simpan tiket baru ke database.
     *
     * Perhatikan: parameter sudah berupa StoreTicketRequest (bukan Request biasa).
     * Laravel otomatis menjalankan validasi di dalam class itu sebelum method ini dipanggil.
     * Jika validasi gagal, Laravel redirect kembali dengan error — kita tidak perlu handle ini.
     */
    public function store(StoreTicketRequest $request): RedirectResponse
    {
        // $request->validated() hanya mengembalikan field yang sudah lolos validasi
        // Lebih aman dari $request->all()
        $ticket = Ticket::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        // Catat aktivitas
        ActivityLog::record(
            $ticket,
            auth()->user(),
            'ticket_created',
            auth()->user()->name . ' membuat tiket ' . $ticket->ticket_number . '.'
        );

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Tiket ' . $ticket->ticket_number . ' berhasil dibuat.');
    }

    /**
     * Detail satu tiket.
     *
     * Konsep: ROUTE MODEL BINDING
     * Laravel otomatis mengambil Ticket dari DB berdasarkan ID di URL.
     * GET /tickets/5 → Laravel cari Ticket::find(5) → inject ke parameter $ticket
     * Jika tidak ada → otomatis 404.
     *
     * Setara dengan @Param('id') + TicketService.findById() di NestJS.
     */
    public function show(Ticket $ticket): View
    {
        // Pastikan user boleh lihat tiket ini (akan kita tambahkan Policy di Phase 5)
        // Untuk sekarang, basic check: user hanya bisa lihat tiket miliknya
        if (auth()->user()->isUser() && $ticket->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        // Load semua relasi yang dibutuhkan halaman detail sekaligus
        $ticket->load([
            'requester',
            'assignedAgent',
            'category',
            'comments.author',   // load komentar + penulis setiap komentar
            'activityLogs.actor', // load log + pelaku setiap log
        ]);

        $agents = User::where('role', 'agent')->orderBy('name')->get();

        return view('tickets.show', compact('ticket', 'agents'));
    }

    /**
     * Update tiket — menangani berbagai jenis update dalam satu method.
     * Status change, assignment, dsb — semua lewat sini.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $user = auth()->user();
        $validated = $request->validated();
        $oldStatus = $ticket->status;
        $oldAgent  = $ticket->assigned_agent_id;

        $ticket->update($validated);

        // Log status change
        if (isset($validated['status']) && $validated['status'] !== $oldStatus) {
            $statusLabels = [
                'open' => 'Open', 'in_progress' => 'In Progress',
                'resolved' => 'Resolved', 'closed' => 'Closed',
            ];
            ActivityLog::record(
                $ticket, $user, 'status_changed',
                $user->name . ' mengubah status dari ' .
                $statusLabels[$oldStatus] . ' menjadi ' .
                $statusLabels[$validated['status']] . '.'
            );
        }

        // Log agent assignment
        if (isset($validated['assigned_agent_id']) && $validated['assigned_agent_id'] !== $oldAgent) {
            $agent = User::find($validated['assigned_agent_id']);
            ActivityLog::record(
                $ticket, $user, 'agent_assigned',
                $user->name . ' menugaskan tiket ke ' . ($agent?->name ?? 'tidak ada') . '.'
            );
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Tiket berhasil diperbarui.');
    }

    /**
     * Tambah komentar ke tiket.
     * Ini bukan bagian dari resource standar, tapi nested route.
     */
    public function storeComment(StoreCommentRequest $request, Ticket $ticket): RedirectResponse
    {
        if (auth()->user()->isUser() && $ticket->user_id !== auth()->id()) {
            abort(403);
        }

        Comment::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'body'      => $request->validated('body'),
        ]);

        ActivityLog::record(
            $ticket, auth()->user(), 'comment_added',
            auth()->user()->name . ' menambahkan komentar.'
        );

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Komentar berhasil ditambahkan.')
            ->fragment('comments');
    }
}
