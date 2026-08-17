<x-app-layout title="Dashboard Saya">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500">Total Tiket</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-blue-100">
            <p class="text-sm text-blue-600">Open</p>
            <p class="text-3xl font-bold text-blue-700 mt-1">{{ $stats['open'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-yellow-100">
            <p class="text-sm text-yellow-600">In Progress</p>
            <p class="text-3xl font-bold text-yellow-700 mt-1">{{ $stats['in_progress'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-green-100">
            <p class="text-sm text-green-600">Resolved</p>
            <p class="text-3xl font-bold text-green-700 mt-1">{{ $stats['resolved'] }}</p>
        </div>
    </div>

    <div class="flex justify-end mb-4">
        <a href="{{ route('tickets.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            ➕ Buat Tiket Baru
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between p-6 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">Tiket Saya</h2>
            <a href="{{ route('tickets.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Lihat semua →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentTickets as $ticket)
                <div class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-4 min-w-0">
                        <span class="text-xs font-mono text-gray-400 flex-shrink-0">{{ $ticket->ticket_number }}</span>
                        <div class="min-w-0">
                            <a href="{{ route('tickets.show', $ticket) }}" class="text-sm font-medium text-gray-800 hover:text-indigo-600 truncate block">
                                {{ $ticket->title }}
                            </a>
                            <p class="text-xs text-gray-400">{{ $ticket->category->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                        <x-priority-badge :priority="$ticket->priority" />
                        <x-status-badge :status="$ticket->status" />
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-gray-400 text-sm mb-3">Belum ada tiket. Buat tiket pertama Anda!</p>
                    <a href="{{ route('tickets.create') }}" class="text-indigo-600 text-sm hover:underline">Buat Tiket →</a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
