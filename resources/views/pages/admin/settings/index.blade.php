<x-layouts::app :title="__('Pengaturan')">
    <div class="space-y-4">
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
                                <flux:label>Alamat Toko</flux:label>
                                <flux:input name="address" rows="2">{{ old('address', $address) }}</flux:input>
                                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </flux:field>

                            <flux:field style="width: 50%">
                                <flux:label>Nomor Telepon</flux:label>
                                <flux:input type="text" name="phone" value="{{ old('phone', $phone) }}" />
                                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </flux:field>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <flux:button type="submit" variant="primary" color="blue">Simpan Pengaturan</flux:button>
                    </div>
                </form>
            </flux:card>

            {{-- Manajemen Diskon Produk & Diskon Anggota Non-Anggota --}}
            <flux:card class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Diskon Produk Khusus</h3>

                <!-- Diskon Produk -->
                <div class="flex flex-col sm:flex-row gap-4" action="{{ route('settings.discount.add') }}"
                    method="POST">
                    <flux:field style="width: 80%;">
                        @csrf
                        <flux:label>Produk</flux:label>
                        <flux:select name="stok_barang_id" required>
                            <option value="">* Pilih Produk *</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->nama_barang }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field style="width: 15%;">
                        <div>
                            <flux:label>Diskon (%)</flux:label>
                            <flux:input type="number" name="harga_diskon" min="0" max="100" step="0.01" required />
                        </div>
                    </flux:field>

                    <div class="flex items-end" style="padding-top: 22px;">
                        <flux:button type="submit" variant="primary" color="blue" icon="plus">
                            Tambah Diskon
                        </flux:button>
                    </div>
                </div>

                <!-- Diskon Anggota dan Non-Anggota -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <flux:field style="width: 50%;">
                        <flux:label>Diskon Anggota (%)</flux:label>
                        <flux:input type="number" name="member_discount"
                            value="{{ old('member_discount', $memberDiscount) }}" min="0" max="100" step="0.01"
                            required />
                        @error('member_discount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </flux:field>

                    <flux:field style="width: 50%;">
                        <flux:label>Diskon Non‑Anggota (%)</flux:label>
                        <flux:input type="number" name="non_member_discount"
                            value="{{ old('non_member_discount', $nonMemberDiscount) }}" min="0" max="100" step="0.01"
                            required />
                        @error('non_member_discount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </flux:field>
                </div>

                {{-- Daftar diskon yang sudah ada --}}
                @if($discountedProducts->count())
                    <div class="space-y-4">
                        @foreach($discountedProducts as $discount)
                            <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-700 rounded-lg">
                                <div>
                                    <span class="font-medium">{{ $discount->stokBarang->nama_barang }}</span>
                                    <span class="text-sm text-zinc-500 ml-2">{{ $discount->harga_diskon }}%</span>
                                </div>
                                <form action="{{ route('settings.discount.remove', $discount->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" variant="primary" size="xs" icon="trash" class="text-red-500">
                                        Hapus
                                    </flux:button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>
        </div>
    </div>
</x-layouts::app>