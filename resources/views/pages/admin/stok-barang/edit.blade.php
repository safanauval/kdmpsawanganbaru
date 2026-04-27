<x-layouts::app :title="__('Edit Stok Barang')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-6">
        <div class="mb-4">
            <flux:heading size="2xl">✏️ Edit Stok Barang</flux:heading>
            <p class="mt-3 text-gray-600 dark:text-gray-400">Perbarui data barang</p>
        </div>

        <div
            class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 dark:bg-neutral-900 p-6">
            <form action="{{ route('stok-barang.update', $stokBarang) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <div>
                            <flux:label class="p-2">Gambar</flux:label>
                            <flux:input class="p-1" type="file" name="gambar" accept="image/jpg/png/jpeg"
                                value="{{ old('gambar', $stokBarang->gambar)}}" nullable>
                            </flux:input>
                            <flux:error name="gambar" />
                        </div>
                        <div>
                            <flux:label class="p-2">Kode Barang</flux:label>
                            <flux:input class="p-1" name="kode_barang"
                                value="{{ old('kode_barang', $stokBarang->kode_barang) }}" required>
                            </flux:input>
                            <flux:error name="kode_barang" />
                        </div>
                        <div>
                            <flux:label class="p-2">Nama Barang</flux:label>
                            <flux:input class="p-1" name="nama_barang"
                                value="{{ old('nama_barang', $stokBarang->nama_barang) }}" required>
                            </flux:input>
                            <flux:error name="nama_barang" />
                        </div>
                        <div>
                            <flux:label class="p-2">Kategori</flux:label>
                            <flux:select class="p-1" name="kategori_id" required>
                                <option value="">- Pilih Kategori </option>
                                @foreach($kategoris as $kat)
                                    <option value="{{ $kat->id }}" {{ old('kategori_id', $stokBarang->kategori_id) == $kat->id ? 'selected' : '' }}>
                                        {{ $kat->nama }}
                                    </option>
                                @endforeach
                            </flux:select>
                            <flux:error name="kategori_id" />
                        </div>
                        <div>
                            <flux:label class="p-2">Stok Awal</flux:label>
                            <flux:input class="p-1" type="number" name="stok"
                                value="{{ old('stok', $stokBarang->stok) }}" min="1" required></flux:input>
                            <flux:error name="stok" />
                        </div>
                        <div>
                            <flux:label class="p-2">Satuan</flux:label>
                            <flux:input class="p-1" type="text" name="satuan"
                                value="{{ old('satuan', $stokBarang->satuan) }}" required>
                            </flux:input>
                            <flux:error name="satuan" />
                        </div>
                        <div>
                            <flux:label class="p-2">Harga Beli (Rp)</flux:label>
                            <flux:input class="p-1" type="number" name="harga_beli"
                                value="{{ old('harga_beli', $stokBarang->harga_beli) }}" min="0" step="0.01" required>
                            </flux:input>
                            <flux:error name="harga_beli" />
                        </div>
                        <div>
                            <flux:label class="p-2">Harga Jual (Rp)</flux:label>
                            <flux:input class="p-1" type="number" name="harga_jual"
                                value="{{ old('harga_jual', $stokBarang->harga_jual) }}" min="0" step="0.01" required>
                            </flux:input>
                            <flux:error name="harga_jual" />
                        </div>
                        <div>
                            <flux:label class="p-2">Deskripsi</flux:label>
                            <flux:textarea class="p-1" name="deskripsi" rows="3">
                                {{ old('deskripsi', $stokBarang->deskripsi) }}
                            </flux:textarea>
                            <flux:error name="deskripsi" />
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-4 pt-2">
                    <flux:button type="submit" variant="primary" color="blue">Perbarui</flux:button>
                    <flux:button href="{{ route('stok-barang.index') }}" variant="danger" wire:navigate>Batal
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>