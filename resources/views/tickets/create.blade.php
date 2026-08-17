<x-app-layout title="Buat Tiket Baru">
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('tickets.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke daftar tiket</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-6">Detail Tiket Baru</h2>

            <form method="POST" action="{{ route('tickets.store') }}" class="space-y-5">
                @csrf

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        Judul Tiket <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}"
                           placeholder="Deskripsi singkat masalah Anda"
                           class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300
                                  {{ $errors->has('title') ? 'border-red-400' : 'border-gray-200' }}">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Category & Priority (2 kolom) --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select id="category_id" name="category_id"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300
                                       {{ $errors->has('category_id') ? 'border-red-400' : 'border-gray-200' }}">
                            <option value="">Pilih kategori...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                            Prioritas <span class="text-red-500">*</span>
                        </label>
                        <select id="priority" name="priority"
                                class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300
                                       {{ $errors->has('priority') ? 'border-red-400' : 'border-gray-200' }}">
                            <option value="">Pilih prioritas...</option>
                            @foreach(['low' => 'Low — Tidak mendesak', 'medium' => 'Medium — Normal', 'high' => 'High — Segera', 'urgent' => 'Urgent — Kritis'] as $value => $label)
                                <option value="{{ $value }}" {{ old('priority') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('priority')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Deskripsi Masalah <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="6"
                              placeholder="Jelaskan masalah Anda secara detail: apa yang terjadi, kapan terjadi, langkah apa yang sudah dicoba..."
                              class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none
                                     {{ $errors->has('description') ? 'border-red-400' : 'border-gray-200' }}">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        Kirim Tiket
                    </button>
                    <a href="{{ route('tickets.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
