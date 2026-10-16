<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kategori Dana
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Form tambah --}}
            <div class="bg-white p-4 shadow sm:rounded-lg">
                <form method="POST" action="{{ route('categories.store') }}" class="flex flex-col sm:flex-row gap-2">
                    @csrf
                    <x-text-input name="name" placeholder="Nama kategori (mis. Makan)" class="flex-1" required />
                    <x-text-input name="balance" type="number" step="1" min="0" placeholder="Saldo awal" class="sm:w-40" />
                    <x-primary-button>Tambah</x-primary-button>
                </form>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                <x-input-error :messages="$errors->get('balance')" class="mt-2" />
            </div>

            {{-- Daftar kategori --}}
            <div class="bg-white shadow sm:rounded-lg divide-y">
                @forelse ($categories as $category)
                    <div class="p-4 flex flex-col sm:flex-row gap-2 sm:items-center">
                        <form method="POST" action="{{ route('categories.update', $category) }}" class="flex flex-col sm:flex-row gap-2 flex-1">
                            @csrf
                            @method('PATCH')
                            <x-text-input name="name" :value="$category->name" class="flex-1" required />
                            <x-text-input name="balance" type="number" step="1" min="0" :value="(int) $category->balance" class="sm:w-40" required />
                            <x-primary-button>Simpan</x-primary-button>
                        </form>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}"
                              onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>Hapus</x-danger-button>
                        </form>
                    </div>
                @empty
                    <p class="p-4 text-gray-500">Belum ada kategori. Tambahkan yang pertama di atas.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
