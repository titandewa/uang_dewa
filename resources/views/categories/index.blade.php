<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kategori Dana
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <p class="text-sm text-gray-600 px-4 sm:px-0">
                Terpakai: <strong>{{ rtrim(rtrim(number_format($usedPercentage, 2, ',', ''), '0'), ',') }}%</strong>
                &middot; Otomatis masuk ke "Sisa": <strong>{{ rtrim(rtrim(number_format(100 - $usedPercentage, 2, ',', ''), '0'), ',') }}%</strong>
            </p>

            {{-- Form tambah --}}
            <div class="bg-white p-4 shadow sm:rounded-lg">
                <form method="POST" action="{{ route('categories.store') }}" class="flex flex-col sm:flex-row gap-2">
                    @csrf
                    <x-text-input name="name" :value="old('name')" placeholder="Nama kategori (mis. Makan)" class="flex-1" required />
                    <x-text-input name="percentage" type="number" step="0.01" min="0" max="100" :value="old('percentage')" placeholder="Persen (%)" class="sm:w-28" required />
                    <x-text-input name="balance" type="number" step="1" min="0" :value="old('balance')" placeholder="Saldo awal" class="sm:w-36" />
                    <x-primary-button>Tambah</x-primary-button>
                </form>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                <x-input-error :messages="$errors->get('percentage')" class="mt-2" />
                <x-input-error :messages="$errors->get('balance')" class="mt-2" />
            </div>

            {{-- Daftar kategori --}}
            <div class="bg-white shadow sm:rounded-lg divide-y">
                @foreach ($categories as $category)
                    @if ($category->is_system)
                        <div class="p-4 flex justify-between items-center bg-gray-50">
                            <div>
                                <p class="font-medium text-gray-800">{{ $category->name }}</p>
                                <p class="text-xs text-gray-500">Otomatis, dari persentase yang belum terbagi</p>
                            </div>
                            <p class="font-semibold text-gray-800">Rp {{ number_format($category->balance, 0, ',', '.') }}</p>
                        </div>
                    @else
                        <div class="p-4 flex flex-col sm:flex-row gap-2 sm:items-center">
                            <form method="POST" action="{{ route('categories.update', $category) }}" class="flex flex-col sm:flex-row gap-2 flex-1">
                                @csrf
                                @method('PATCH')
                                <x-text-input name="name" :value="$category->name" class="flex-1" required />
                                <x-text-input name="percentage" type="number" step="0.01" min="0" max="100" :value="(float) $category->percentage" class="sm:w-28" required />
                                <x-text-input name="balance" type="number" step="1" min="0" :value="(int) $category->balance" class="sm:w-36" required />
                                <x-primary-button>Simpan</x-primary-button>
                            </form>
                            <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                  onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                                @csrf
                                @method('DELETE')
                                <x-danger-button>Hapus</x-danger-button>
                            </form>
                        </div>
                    @endif
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
