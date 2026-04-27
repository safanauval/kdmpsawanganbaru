<div class="relative overflow-hidden rounded-xl dark:border-neutral-700 sm:p-8 p-2">
    <flux:table>
        <flux:table.columns>
            <flux:table.column class="w-16">No</flux:table.column>
            <flux:table.column align="end">Nama Kategori</flux:table.column>
            <flux:table.column align="end">Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($kategoris as $kategori)
                <flux:table.row>
                    <flux:table.cell class="text-neutral-500 dark:text-neutral-400">
                        {{ ($kategoris->currentPage() - 1) * $kategoris->perPage() + $loop->iteration }}
                    </flux:table.cell>
                    <flux:table.cell variant="strong" align="end">{{ $kategori->nama }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <div colspan="2" class="flex justify-end gap-2">
                            <flux:button.group>
                                <flux:button href="{{ route('kategori.edit', $kategori) }}" size="xs" icon="pencil-square"
                                    wire:navigate>
                                    Edit
                                </flux:button>
                                <form action="{{ route('kategori.destroy', $kategori) }}" method="POST"
                                    onsubmit="return confirm('Yakin hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" size="xs" icon="trash" variant="danger">
                                        Hapus
                                    </flux:button>
                                </form>
                            </flux:button.group>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="3" class="text-center py-8 text-neutral-500 dark:text-neutral-400">
                        Belum ada data kategori.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @if($kategoris->hasPages())
        <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700">
            {{ $kategoris->links() }}
        </div>
    @endif
</div>