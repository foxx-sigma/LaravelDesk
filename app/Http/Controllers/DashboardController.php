<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * DashboardController — mengarahkan user ke dashboard yang sesuai dengan role.
 *
 * Satu route /dashboard → tiga tampilan berbeda berdasarkan role user.
 * Ini lebih bersih dari kondisi if-else di dalam satu view.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        if ($user->isAgent()) {
            return $this->agentDashboard();
        }

        return $this->userDashboard();
    }

    private function adminDashboard(): View
    {
        // Import model yang dibutuhkan
        $ticketQuery = \App\Models\Ticket::query();

        $stats = [
            'total'       => $ticketQuery->count(),
            'open'        => (clone $ticketQuery)->where('status', 'open')->count(),
            'in_progress' => (clone $ticketQuery)->where('status', 'in_progress')->count(),
            'resolved'    => (clone $ticketQuery)->where('status', 'resolved')->count(),
            'closed'      => (clone $ticketQuery)->where('status', 'closed')->count(),
        ];

        // 5 tiket terbaru — eager load relasi agar tidak N+1 query
        $recentTickets = \App\Models\Ticket::with(['requester', 'category', 'assignedAgent'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact('stats', 'recentTickets'));
    }

    private function agentDashboard(): View
    {
        $agent = auth()->user();

        $stats = [
            'assigned'    => $agent->assignedTickets()->count(),
            'open'        => $agent->assignedTickets()->where('status', 'open')->count(),
            'in_progress' => $agent->assignedTickets()->where('status', 'in_progress')->count(),
            'resolved'    => $agent->assignedTickets()->where('status', 'resolved')->count(),
        ];

        $recentTickets = $agent->assignedTickets()
            ->with(['requester', 'category'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.agent', compact('stats', 'recentTickets'));
    }

    private function userDashboard(): View
    {
        $user = auth()->user();

        $stats = [
            'total'       => $user->tickets()->count(),
            'open'        => $user->tickets()->where('status', 'open')->count(),
            'in_progress' => $user->tickets()->where('status', 'in_progress')->count(),
            'resolved'    => $user->tickets()->where('status', 'resolved')->count(),
        ];

        $recentTickets = $user->tickets()
            ->with(['category', 'assignedAgent'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.user', compact('stats', 'recentTickets'));
    }
}
