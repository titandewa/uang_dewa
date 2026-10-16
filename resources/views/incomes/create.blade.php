<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pemasukan
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6"
             x-data="{
                 amount: '{{ old('amount') }}',
                 envelopes: @js($envelopes),
                 share(p) { return Math.floor((Number(this.amount) || 0) * p / 100); },
                 get rest() {
                     const total = Number(this.amount) || 0;
                     return total - this.envelopes.reduce((sum, c) => sum + this.share(c.percentage), 0);
                 },
                 rupiah(n) { return 'Rp ' + n.toLocaleString('id-ID'); }
             }">

            <form method="POST" action="{{ route('incomes.store') }}" class="bg-white p-4 shadow sm:rounded-lg space-y-3">
                @csrf

                <div>
                    <x-text-input name="amount" type="number" min="1" step="1" x-model="amount"
                                  placeholder="Nominal pemasukan" class="w-full" required />
                    <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                </div>

                <div>
                    <x-text-input name="note" :value="old('note')" placeholder="Catatan (opsional, mis. Uang saku)" class="w-full" />
                    <x-input-error :messages="$errors->get('note')" class="mt-1" />
                </div>

                <div>
                    <x-text-input name="transaction_date" type="date"
                                  :value="old('transaction_date', now()->toDateString())" class="w-full" required />
                    <x-input-error :messages="$errors->get('transaction_date')" class="mt-1" />
                </div>

                <x-primary-button>Simpan Pemasukan</x-primary-button>
            </form>

            {{-- Pratinjau pembagian --}}
            <div class="bg-white p-4 shadow sm:rounded-lg" x-show="Number(amount) > 0" x-cloak>
                <p class="font-medium text-gray-800 mb-2">Pembagian ke amplop</p>

                <template x-for="(c, i) in envelopes" :key="i">
                    <div class="flex justify-between text-sm py-1">
                        <span x-text="c.name + ' (' + c.percentage + '%)'"></span>
                        <span x-text="rupiah(share(c.percentage))"></span>
                    </div>
                </template>

                <div class="flex justify-between text-sm py-1 border-t mt-1 pt-2 text-gray-600">
                    <span>Sisa</span>
                    <span x-text="rupiah(rest)"></span>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
