<x-layouts::app :title="__('Tambah Kategori')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-6">
        <div class="mb-4">
            <flux:heading size="2xl">➕ Tambah Kategori</flux:heading>
            <p class="mt-3 text-gray-600 dark:text-gray-400">Masukkan data kategori baru</p>
        </div>

        <div class="max-w-2xl">
            <div
                class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 dark:bg-neutral-900 p-6">
                <form action="{{ route('kategori.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>Nama Kategori</flux:label>
                            <flux:input name="nama" value="{{ old('nama') }}" required autofocus />
                            <flux:error name="nama" />
                        </flux:field>
                        <div class="flex justify-end gap-3 mt-4 pt-2">
                            <flux:button type="submit" variant="primary" color="blue">Simpan</flux:button>
                            <flux:button href="{{ route('kategori.index') }}" variant="danger" wire:navigate>Batal
                            </flux:button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts::app>