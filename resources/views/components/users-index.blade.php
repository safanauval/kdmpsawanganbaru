<div x-data x-on:notify.window="Flux.toast({ text: $event.detail[0], variant: $event.detail[1] ?? 'success' })"
    class="flex h-full w-full flex-1 flex-col gap-2 rounded-xl">
    {{-- Header dengan Search dan Tombol Tambah --}}
    <div class="flex gap-2">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email..."
                icon="magnifying-glass" clearable />
        </div>
        <flux:button wire:click="openCreateModal" variant="primary" color="blue" icon="plus">
            Tambah Anggota
        </flux:button>
    </div>

    {{-- Tabel User --}}
    <div class="relative overflow-y-auto rounded-xl border-neutral-200 dark:border-neutral-700 p-1">
        <flux:table>
            <flux:table.columns>
                <flux:table.column class="w-16">No</flux:table.column>
                <flux:table.column>Nama</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column align="center">Role</flux:table.column>
                <flux:table.column align="end">Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($users as $user)
                    <flux:table.row wire:key="user-{{ $user->id }}">
                        <flux:table.cell class="text-neutral-500 dark:text-neutral-400">
                            {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                        </flux:table.cell>
                        <flux:table.cell variant="strong">{{ $user->name }}</flux:table.cell>
                        <flux:table.cell>{{ $user->email }}</flux:table.cell>
                        <flux:table.cell align="center">
                            @if($editingUserId === $user->id)
                                <flux:select wire:model="selectedRole" class="w-32">
                                    <option value="admin">Admin</option>
                                    <option value="kasir">Kasir</option>
                                </flux:select>
                            @else
                                <flux:badge size="sm" color="{{ $user->role === 'admin' ? 'blue' : 'yellow' }}"
                                    inset="top bottom">
                                    {{ ucfirst($user->role) }}
                                </flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell align="end" class="py-2">
                            @if($editingUserId === $user->id)
                                <div style="padding-left: 15px;">
                                    <flux:button.group>
                                        <flux:button wire:click="updateRole({{ $user->id }})" size="sm" variant="primary"
                                            color="blue">
                                            Simpan
                                        </flux:button>
                                        <flux:button wire:click="cancelEditing" size="sm" variant="danger">
                                            Batal
                                        </flux:button>
                                    </flux:button.group>
                                </div>
                            @else
                                <div class="flex gap-2 justify-end">
                                    <flux:button wire:key="edit-{{ $user->id }}" wire:click="startEditing({{ $user->id }})"
                                        size="sm" icon="pencil-square">
                                        Ubah Role
                                    </flux:button>
                                </div>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center py-8 text-neutral-500 dark:text-neutral-400">
                            @if($search)
                                Tidak ditemukan user dengan kata kunci "{{ $search }}".
                            @else
                                Belum ada data user.
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-neutral-200 dark:border-neutral-700">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- ========== MODAL TAMBAH ANGGOTA ========== --}}
    <flux:modal wire:model="showCreateModal" title="Tambah Anggota Baru">
        <form wire:submit="createUser" class="space-y-6">
            <flux:field>
                <flux:label>Nama Lengkap</flux:label>
                <flux:input wire:model="name" placeholder="Masukkan nama lengkap" required />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Email</flux:label>
                <flux:input type="email" wire:model="email" placeholder="email@example.com" required />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>Password</flux:label>
                <flux:input type="password" wire:model="password" placeholder="Minimal 8 karakter" required />
                <flux:error name="password" />
            </flux:field>

            <flux:field>
                <flux:label>Konfirmasi Password</flux:label>
                <flux:input type="password" wire:model="password_confirmation" placeholder="Ulangi password" required />
                <flux:error name="password_confirmation" />
            </flux:field>

            <flux:field>
                <flux:label>Role</flux:label>
                <flux:select wire:model="newUser">
                    <option value="kasir">Kasir</option>
                    <option value="admin">Admin</option>
                </flux:select>
                <flux:error name="newUser" />
            </flux:field>

            <div class="flex justify-end gap-3">
                <flux:button type="button" wire:click="closeCreateModal" variant="danger">Batal</flux:button>
                <flux:button type="submit" variant="primary" color="blue">Simpan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
