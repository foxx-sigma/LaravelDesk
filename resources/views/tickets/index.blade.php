<x-app-layout title="Daftar Tiket">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Semua Tiket</h2>
            <p class="text-sm text-gray-500">{{ $tickets->total() }} tiket ditemukan</p>
        </div>
        @if(auth()->user()->isUser())
            <a href="{{ route('tickets.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                ➕ Buat Tiket
            </a>
        @endif
    </div>

    {{-- FILTER & SEARCH --}}
    <form method="GET" action="{{ route('tickets.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-wrap gap-3">
            {{-- Search --}}
            <div class="flex-1 min-w-48">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nomor atau judul tiket..."
                       class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>

            {{-- Status filter --}}
            <select name="status" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <option value="">Semua Status</option>
                @foreach(['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            {{-- Priority filter --}}
            <select name="priority" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <option value="">Semua Prioritas</option>
                @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'] as $value => $label)
                    <option value="{{ $value }}" {{ request('priority') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            {{-- Category filter --}}
            <select name="category_id" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'priority', 'category_id']))
                <a href="{{ route('tickets.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-800 transition-colors">
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- TICKET TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if($tickets->isEmpty())
            <div class="p-12 text-center">
                <p class="text-4xl mb-3">🎫</p>
                <p class="text-gray-500 text-sm">Tidak ada tiket yang cocok dengan filter.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Tiket</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Prioritas</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            @if(!auth()->user()->isUser())
                                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemohon</th>
                            @endif
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Dibuat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs text-gray-500">{{ $ticket->ticket_number }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-gray-800 hover:text-indigo-600 line-clamp-1">
                                        {{ $ticket->title }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $ticket->category->name ?? '-' }}</td>
                                <td class="px-4 py-3"><x-priority-badge :priority="$ticket->priority" /></td>
                                <td class="px-4 py-3"><x-status-badge :status="$ticket->status" /></td>
                                @if(!auth()->user()->isUser())
                                    <td class="px-4 py-3 text-gray-500">{{ $ticket->requester->name ?? '-' }}</td>
                                @endif
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $ticket->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($tickets->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $tickets->links() }}
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
