<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — LaravelDesk' : 'LaravelDesk' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">

    {{-- Sidebar + Content Layout --}}
    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        <aside class="hidden md:flex md:flex-col w-64 bg-gray-900 text-white flex-shrink-0">
            {{-- Logo --}}
            <div class="flex items-center h-16 px-6 border-b border-gray-700">
                <a href="{{ route('dashboard') }}" class="text-xl font-bold text-white tracking-tight">
                    🎫 LaravelDesk
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    📊 Dashboard
                </x-sidebar-link>

                <x-sidebar-link href="{{ route('tickets.index') }}" :active="request()->routeIs('tickets.*')">
                    🎫 Tiket
                </x-sidebar-link>

                @if(auth()->user()->isUser())
                    <x-sidebar-link href="{{ route('tickets.create') }}" :active="false">
                        ➕ Buat Tiket
                    </x-sidebar-link>
                @endif

                @if(auth()->user()->isAdmin())
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Admin</p>
                    </div>
                    <x-sidebar-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
                        👥 Pengguna
                    </x-sidebar-link>
                    <x-sidebar-link href="{{ route('admin.categories.index') }}" :active="request()->routeIs('admin.categories.*')">
                        🏷️ Kategori
                    </x-sidebar-link>
                @endif
            </nav>

            {{-- User Info --}}
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-sm font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full text-left text-xs text-gray-400 hover:text-white transition-colors">
                        Logout →
                    </button>
                </form>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Top bar --}}
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 flex-shrink-0">
                <h1 class="text-lg font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h1>
                <div class="text-sm text-gray-500">
                    {{ now()->format('d M Y') }}
                </div>
            </header>

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm flex items-center gap-2">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm flex items-center gap-2">
                    ❌ {{ session('error') }}
                </div>
            @endif

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

</body>
</html>
