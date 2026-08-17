<x-app-layout :title="$ticket->ticket_number">
    <div class="mb-4">
        <a href="{{ route('tickets.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke daftar tiket</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI: Detail tiket + Comments + Activity --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Ticket Header --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="min-w-0">
                        <p class="text-xs font-mono text-gray-400 mb-1">{{ $ticket->ticket_number }}</p>
                        <h2 class="text-lg font-semibold text-gray-800">{{ $ticket->title }}</h2>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <x-priority-badge :priority="$ticket->priority" />
                        <x-status-badge :status="$ticket->status" />
                    </div>
                </div>
                <div class="prose prose-sm text-gray-600 max-w-none">
                    {{ $ticket->description }}
                </div>
            </div>

            {{-- Comments --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100" id="comments">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700">Percakapan ({{ $ticket->comments->count() }})</h3>
                </div>

                <div class="divide-y divide-gray-50">
                    @forelse($ticket->comments as $comment)
                        <div class="p-5 {{ $comment->user_id === auth()->id() ? 'bg-indigo-50/30' : '' }}">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-700">
                                    {{ strtoupper(substr($comment->author->name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-800">{{ $comment->author->name }}</span>
                                    <span class="ml-2 text-xs text-gray-400 capitalize bg-gray-100 px-1.5 py-0.5 rounded">{{ $comment->author->role }}</span>
                                </div>
                                <span class="ml-auto text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap pl-9">{{ $comment->body }}</p>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400 text-sm">Belum ada komentar.</div>
                    @endforelse
                </div>

                {{-- Add Comment Form --}}
                @if(!$ticket->isClosed())
                    <div class="p-5 border-t border-gray-100 bg-gray-50/50">
                        <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}">
                            @csrf
                            <textarea name="body" rows="3" placeholder="Tulis komentar..."
                                      class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none bg-white">{{ old('body') }}</textarea>
                            @error('body') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            <div class="mt-2 flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                                    Kirim Komentar
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Activity Log --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700">Riwayat Aktivitas</h3>
                </div>
                <div class="p-5 space-y-3">
                    @forelse($ticket->activityLogs as $log)
                        <div class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-gray-300 mt-2 flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-700">{{ $log->description }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Info + Actions --}}
        <div class="space-y-6">

            {{-- Ticket Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Informasi Tiket</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Pemohon</dt>
                        <dd class="font-medium text-gray-800">{{ $ticket->requester->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Kategori</dt>
                        <dd class="font-medium text-gray-800">{{ $ticket->category->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Agent</dt>
                        <dd class="font-medium text-gray-800">{{ $ticket->assignedAgent->name ?? 'Belum ditugaskan' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Dibuat</dt>
                        <dd class="text-gray-600">{{ $ticket->created_at->format('d M Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Diperbarui</dt>
                        <dd class="text-gray-600">{{ $ticket->updated_at->diffForHumans() }}</dd>
                    </div>
                </dl>
            </div>

            {{-- ACTIONS berdasarkan role --}}

            {{-- Admin: assign agent + ubah status --}}
            @if(auth()->user()->isAdmin())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Kelola Tiket</h3>

                    <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="space-y-3">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Assign Agent</label>
                            <select name="assigned_agent_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <option value="">Tidak ada</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ $ticket->assigned_agent_id == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                @foreach(['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $value => $label)
                                    <option value="{{ $value }}" {{ $ticket->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="w-full py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            @endif

            {{-- Agent: ubah status --}}
            @if(auth()->user()->isAgent() && $ticket->assigned_agent_id === auth()->id())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Update Status</h3>
                    <form method="POST" action="{{ route('tickets.update', $ticket) }}">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300 mb-3">
                            @foreach(['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved'] as $value => $label)
                                <option value="{{ $value }}" {{ $ticket->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                            Update Status
                        </button>
                    </form>
                </div>
            @endif

            {{-- User: tutup tiket jika resolved --}}
            @if(auth()->user()->isUser() && $ticket->isResolved() && $ticket->user_id === auth()->id())
                <div class="bg-white rounded-xl shadow-sm border border-green-100 p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Tiket Resolved</h3>
                    <p class="text-xs text-gray-500 mb-4">Apakah masalah Anda sudah terselesaikan? Tutup tiket ini.</p>
                    <form method="POST" action="{{ route('tickets.update', $ticket) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="closed">
                        <button type="submit" class="w-full py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                            ✅ Tutup Tiket
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
