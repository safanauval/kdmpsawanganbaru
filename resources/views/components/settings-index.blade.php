<div x-data x-on:notify.window="Flux.toast({ text: $event.detail[0], variant: $event.detail[1] ?? 'success' })" class="space-y-4" >
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl">Pengaturan</flux:heading>
                <p class="mt-3 text-gray-600 dark:text-gray-400">Kelola pengaturan diskon dan informasi koperasi
                    Anda.</p>
            </div>
        </div>

        <div class="space-y-4">
            {{-- Form Diskon & Info Toko --}}
            <flux:card>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pengaturan Umum</h3>
                <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 gap-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                           <flux:field style="width: 50%">
                                <flux:label>Nama Toko</flux:label>
                                <flux:input 
                                    name="company_name" 
                                    value="{{ old('company_name', $company_name ?? '') }}" 
                                />
                                @error('company_name') 
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                                @enderror
                            </flux:field>

                            <flux:field style="width: 50%">
                                <flux:label>Alamat Toko</flux:label>
                                <flux:input 
                                    name="address" 
                                    value="{{ old('address', $address ?? '') }}" 
                                />
                                @error('address') 
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                                @enderror
                            </flux:field>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 gap-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <flux:field style="width: 50%">
                                <flux:label>Nomor Telepon</flux:label>
                                <flux:input type="text" name="phone" value="{{ old('phone', $phone) }}" />
                                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </flux:field>

                            <flux:field style="width: 50%">
                                <flux:label>Catatan kaki</flux:label>
                                <flux:input type="text" name="footer_text" value="{{ old('footer_text', $footer_text) }}" />
                                @error('footer_text') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </flux:field>
                        </div>
                    </div>

                    {{-- ========== DISKON ANGGOTA & NON-ANGGOTA ========== --}}
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Diskon Anggota dan Non Anggota</h3>
                    <div class="flex flex-col sm:flex-row gap-4 items-end">
                        <flux:field style="width: 50%;">
                            <flux:label>Diskon Anggota (%)</flux:label>
                            <flux:input type="text" name="member_discount" value="{{ old('member_discount', $member_discount) }}" />
                                @error('member_discount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </flux:field>

                        <flux:field style="width: 50%;">
                            <flux:label>Diskon Non‑Anggota (%)</flux:label>
                            <flux:input type="text" name="non_member_discount" value="{{ old('non_member_discount', $non_member_discount) }}" />
                                @error('non_member_dicount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </flux:field>
                    </div>
                    <div class="flex justify-end">
                        <flux:button wire:click="updateSettings" variant="primary" color="blue">Simpan Pengaturan</flux:button>
                    </div>
                </form>
            </flux:card>

            

            {{-- ========== DISKON PRODUK KHUSUS ========== --}}
            <flux:card class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Diskon Produk Khusus</h3>

                {{-- Form Tambah Diskon Produk --}}
                <form wire:submit="addProductDiscount">
                    <div class="flex flex-col sm:flex-row gap-4 items-end">
                        <flux:field style="width: 65%;">
                            <flux:label>Produk</flux:label>
                            <flux:select wire:model="stok_barang_id" required>
                                <option value="">- Pilih Produk -</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->nama_barang }}</option>
                                @endforeach
                            </flux:select>
                            @error('stok_barang_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </flux:field>

                        <flux:field style="width: 20%;">
                            <flux:label>Diskon (%)</flux:label>
                            <flux:input type="number" wire:model="harga_diskon" min="0" max="100" step="0.01" />
                            @error('harga_diskon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </flux:field>

                        <div style="padding-top: 27px;" class="flex justify-end">
                            <flux:button type="submit" variant="primary" color="blue" icon="plus">Tambah</flux:button>
                        </div>
                    </div>
                </form>

                {{-- Daftar diskon yang sudah ada --}}
                @if($discountedProducts->count())
                    <div class="space-y-3 mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                        <h4 class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Diskon Produk Aktif</h4>
                        
                        @foreach($discountedProducts as $discount)
                            <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                                <div>
                                    <span>{{ $discount->stokBarang->nama_barang }}</span>
                                    <flux:badge size="sm" color="blue" class="ml-2">{{ $discount->harga_diskon }}%</flux:badge>
                                </div>
                                <flux:button wire:click="removeProductDiscount({{ $discount->id }})" wire:confirm="Hapus diskon ini?" size="sm" icon="trash" variant="primary" color="red">
                                    Hapus Diskon
                                </flux:button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>
        </div>
    </div>