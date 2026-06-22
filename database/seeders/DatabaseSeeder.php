<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Nauval',
            'email' => 'admin@gmail.com',
            'password' => 'admin123',
            'role' => "admin",
        ]);

        User::factory()->create([
            'name' => 'pall',
            'email' => 'kasir@gmail.com',
            'password' => 'kasir123',
            'role' => "kasir",
        ]);

        \DB::table('kategori')->insert([
            ['id' => 1, 'nama' => 'Makanan', 'parent_id' => null, 'created_at' => '2026-05-11 03:57:38', 'updated_at' => '2026-05-11 03:57:38'],
            ['id' => 2, 'nama' => 'Minuman', 'parent_id' => null, 'created_at' => '2026-05-11 03:57:47', 'updated_at' => '2026-05-11 03:57:47'],
            ['id' => 3, 'nama' => 'Sabun Mandi', 'parent_id' => null, 'created_at' => '2026-05-11 03:58:01', 'updated_at' => '2026-05-11 03:58:01'],
        ]);

        \DB::table('gudang')->insert([
            ['id' => 1, 'kode_gudang' => 'GD001', 'nama_gudang' => 'Gudang Utama', 'alamat' => 'Jl. Merdeka No. 1', 'telepon' => '021-1234567', 'created_at' => '2026-05-11 03:57:38', 'updated_at' => '2026-05-11 03:57:38'],
            ['id' => 2, 'kode_gudang' => 'GD002', 'nama_gudang' => 'Gudang Cabang', 'alamat' => 'Jl. Sudirman No. 10', 'telepon' => '021-7654321', 'created_at' => '2026-05-11 03:57:47', 'updated_at' => '2026-05-11 03:57:47'],
            ['id' => 3, 'kode_gudang' => 'GD003', 'nama_gudang' => 'Gudang Reseller', 'alamat' => 'Jl. Diponegoro No. 5', 'telepon' => '021-4567890', 'created_at' => '2026-05-11 03:58:01', 'updated_at' => '2026-05-11 03:58:01'],
        ]);

        \DB::table('stok_barang')->insert([
            ['id' => 1, 'kode_barang' => 'AQU00001', 'nama_barang' => 'Aqua Botol', 'kategori_id' => 2, 'gudang_id' => 1, 'stok' => 200, 'harga_beli' => 2000, 'harga_jual' => 3500, 'satuan' => 'Botol', 'deskripsi' => NULL, 'gambar' => NULL, 'created_at' => '2026-05-11 04:00:00', 'updated_at' => '2026-05-11 04:00:00', 'deleted_at' => NULL],
            ['id' => 2, 'kode_barang' => 'INDM0001', 'nama_barang' => 'Indomie Goreng', 'kategori_id' => 1, 'gudang_id' => 2, 'stok' => 150, 'harga_beli' => 2500, 'harga_jual' => 4000, 'satuan' => 'Pcs', 'deskripsi' => NULL, 'gambar' => NULL, 'created_at' => '2026-05-11 04:05:00', 'updated_at' => '2026-05-11 04:05:00', 'deleted_at' => NULL],
            ['id' => 3, 'kode_barang' => 'LFB0001', 'nama_barang' => 'Lifebuoy', 'kategori_id' => 3, 'gudang_id' => 3, 'stok' => 200, 'harga_beli' => 20000, 'harga_jual' => 25000, 'satuan' => 'Pack', 'deskripsi' => NULL, 'gambar' => NULL, 'created_at' => '2026-05-11 04:00:00', 'updated_at' => '2026-05-11 04:00:00', 'deleted_at' => NULL],
            ['id' => 4, 'kode_barang' => 'AQU00002', 'nama_barang' => 'Aqua Galon', 'kategori_id' => 2, 'gudang_id' => 2, 'stok' => 100, 'harga_beli' => 15000, 'harga_jual' => 17000, 'satuan' => 'Galon', 'deskripsi' => NULL, 'gambar' => NULL, 'created_at' => '2026-05-11 04:10:00', 'updated_at' => '2026-05-11 04:10:00', 'deleted_at' => NULL],
        ]);

        \DB::table('settings')->insert([
            ['id' => 1, 'key' => 'company_name', 'value' => 'Unit Usaha KMP Sawangan Baru'],
            ['id' => 2, 'key' => 'address', 'value' => 'Jl. H. Maksum No.4/3, Sawangan Baru, Kec. Sawangan, Kota Depok, Jawa Barat 16511'],
            ['id' => 3, 'key' => 'phone', 'value' => '085711321108'],
            ['id' => 4, 'key' => 'footer_text', 'value' => 'Terima kasih sudah berbelanja :)'],
            ['id' => 5, 'key' => 'member_discount', 'value' => '10'],
            ['id' => 6, 'key' => 'non_member_discount', 'value' => '0'],
        ]);
    }
}
