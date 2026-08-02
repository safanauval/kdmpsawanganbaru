<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Nauval',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'kasir@gmail.com'],
            [
                'name' => 'pall',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
            ]
        );

        \DB::table('kategori')->updateOrInsert(
            ['id' => 1],
            ['nama' => 'Makanan', 'parent_id' => null, 'created_at' => '2026-05-11 03:57:38', 'updated_at' => '2026-05-11 03:57:38']
        );
        \DB::table('kategori')->updateOrInsert(
            ['id' => 2],
            ['nama' => 'Minuman', 'parent_id' => null, 'created_at' => '2026-05-11 03:57:47', 'updated_at' => '2026-05-11 03:57:47']
        );
        \DB::table('kategori')->updateOrInsert(
            ['id' => 3],
            ['nama' => 'Sabun Mandi', 'parent_id' => null, 'created_at' => '2026-05-11 03:58:01', 'updated_at' => '2026-05-11 03:58:01']
        );

        \DB::table('gudang')->updateOrInsert(
            ['id' => 1],
            ['kode_gudang' => 'GD001', 'nama_gudang' => 'Gudang Utama', 'alamat' => 'Jl. Merdeka No. 1', 'telepon' => '021-1234567', 'created_at' => '2026-05-11 03:57:38', 'updated_at' => '2026-05-11 03:57:38']
        );
        \DB::table('gudang')->updateOrInsert(
            ['id' => 2],
            ['kode_gudang' => 'GD002', 'nama_gudang' => 'Gudang Cabang', 'alamat' => 'Jl. Sudirman No. 10', 'telepon' => '021-7654321', 'created_at' => '2026-05-11 03:57:47', 'updated_at' => '2026-05-11 03:57:47']
        );
        \DB::table('gudang')->updateOrInsert(
            ['id' => 3],
            ['kode_gudang' => 'GD003', 'nama_gudang' => 'Gudang Reseller', 'alamat' => 'Jl. Diponegoro No. 5', 'telepon' => '021-4567890', 'created_at' => '2026-05-11 03:58:01', 'updated_at' => '2026-05-11 03:58:01']
        );

        \DB::table('stok_barang')->updateOrInsert(
            ['id' => 1],
            ['kode_barang' => 'AQU00001', 'nama_barang' => 'Aqua Botol', 'kategori_id' => 2, 'gudang_id' => 1, 'stok' => 200, 'harga_beli' => 2000, 'harga_jual' => 3500, 'satuan' => 'Botol', 'deskripsi' => null, 'gambar' => null, 'created_at' => '2026-05-11 04:00:00', 'updated_at' => '2026-05-11 04:00:00', 'deleted_at' => null]
        );
        \DB::table('stok_barang')->updateOrInsert(
            ['id' => 2],
            ['kode_barang' => 'INDM0001', 'nama_barang' => 'Indomie Goreng', 'kategori_id' => 1, 'gudang_id' => 2, 'stok' => 150, 'harga_beli' => 2500, 'harga_jual' => 4000, 'satuan' => 'Pcs', 'deskripsi' => null, 'gambar' => null, 'created_at' => '2026-05-11 04:05:00', 'updated_at' => '2026-05-11 04:05:00', 'deleted_at' => null]
        );
        \DB::table('stok_barang')->updateOrInsert(
            ['id' => 3],
            ['kode_barang' => 'LFB0001', 'nama_barang' => 'Lifebuoy', 'kategori_id' => 3, 'gudang_id' => 3, 'stok' => 200, 'harga_beli' => 20000, 'harga_jual' => 25000, 'satuan' => 'Pack', 'deskripsi' => null, 'gambar' => null, 'created_at' => '2026-05-11 04:00:00', 'updated_at' => '2026-05-11 04:00:00', 'deleted_at' => null]
        );
        \DB::table('stok_barang')->updateOrInsert(
            ['id' => 4],
            ['kode_barang' => 'AQU00002', 'nama_barang' => 'Aqua Galon', 'kategori_id' => 2, 'gudang_id' => 2, 'stok' => 100, 'harga_beli' => 15000, 'harga_jual' => 17000, 'satuan' => 'Galon', 'deskripsi' => null, 'gambar' => null, 'created_at' => '2026-05-11 04:10:00', 'updated_at' => '2026-05-11 04:10:00', 'deleted_at' => null]
        );

        \DB::table('settings')->updateOrInsert(
            ['id' => 1],
            ['key' => 'company_name', 'value' => 'Unit Usaha KMP Sawangan Baru']
        );
        \DB::table('settings')->updateOrInsert(
            ['id' => 2],
            ['key' => 'address', 'value' => 'Jl. H. Maksum No.4/3, Sawangan Baru, Kec. Sawangan, Kota Depok, Jawa Barat 16511']
        );
        \DB::table('settings')->updateOrInsert(
            ['id' => 3],
            ['key' => 'phone', 'value' => '085711321108']
        );
        \DB::table('settings')->updateOrInsert(
            ['id' => 4],
            ['key' => 'footer_text', 'value' => 'Terima kasih sudah berbelanja :)']
        );
        \DB::table('settings')->updateOrInsert(
            ['id' => 5],
            ['key' => 'member_discount', 'value' => '10']
        );
        \DB::table('settings')->updateOrInsert(
            ['id' => 6],
            ['key' => 'non_member_discount', 'value' => '0']
        );

        \DB::table('anggota')->updateOrInsert(
            ['id_anggota' => 1],
            [
                'kode_anggota' => 'KMPSB001030726',
                'nama_anggota' => 'Safa Nauval Nugraha',
                'email_anggota' => 'safanauval@gmail.com',
                'telepon_anggota' => '-',
                'alamat_anggota' => '-',
                'tanggal_masuk' => '2026-07-03',
                'created_at' => '2026-07-03 07:07:07',
                'updated_at' => '2026-07-03 07:07:07',
            ]
        );

        \DB::table('simpan')->updateOrInsert(
            ['id' => 1],
            [
                'id_anggota' => '1',
                'jenis_simpanan' => 'pokok',
                'jumlah' => 100000,
                'payment_method' => 'tunai',
                'tanggal' => '2026-07-03',
                'total_simpanan' => '100000',
                'created_at' => '2026-07-03 07:07:07',
                'updated_at' => '2026-07-03 07:07:07',
            ]
        );
    }
}
