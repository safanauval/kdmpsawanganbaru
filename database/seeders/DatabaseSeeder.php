<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ========== 1. USERS ==========
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Nauval',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir@gmail.com'],
            [
                'name' => 'Kasir Utama',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
                'email_verified_at' => now(),
            ]
        );

        echo "✅ Users seeded/updated successfully!\n";

        // ========== 2. KATEGORI ==========
        $kategoris = [
            ['id' => 1, 'nama' => 'Makanan'],
            ['id' => 2, 'nama' => 'Minuman'],
            ['id' => 3, 'nama' => 'Sabun Mandi'],
        ];

        foreach ($kategoris as $kategori) {
            DB::table('kategori')->updateOrInsert(
                ['id' => $kategori['id']], // Cek by ID
                [
                    'nama' => $kategori['nama'],
                    'parent_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        echo "✅ Kategori seeded successfully!\n";

        // ========== 3. GUDANG ==========
        $gudangs = [
            ['id' => 1, 'kode_gudang' => 'GD001', 'nama_gudang' => 'Gudang Utama', 'alamat' => 'Jl. Merdeka No. 1', 'telepon' => '021-1234567'],
            ['id' => 2, 'kode_gudang' => 'GD002', 'nama_gudang' => 'Gudang Cabang', 'alamat' => 'Jl. Sudirman No. 10', 'telepon' => '021-7654321'],
            ['id' => 3, 'kode_gudang' => 'GD003', 'nama_gudang' => 'Gudang Reseller', 'alamat' => 'Jl. Diponegoro No. 5', 'telepon' => '021-4567890'],
        ];

        foreach ($gudangs as $gudang) {
            DB::table('gudang')->updateOrInsert(
                ['id' => $gudang['id']],
                array_merge($gudang, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        echo "✅ Gudang seeded successfully!\n";

        // ========== 4. STOK BARANG ==========
        $barangs = [
            [
                'id' => 1,
                'kode_barang' => 'AQU00001',
                'nama_barang' => 'Aqua Botol',
                'kategori_id' => 2,
                'gudang_id' => 1,
                'stok' => 200,
                'harga_beli' => 2000,
                'harga_jual' => 3500,
                'satuan' => 'Botol',
                'deskripsi' => null,
                'gambar' => null,
            ],
            [
                'id' => 2,
                'kode_barang' => 'INDM0001',
                'nama_barang' => 'Indomie Goreng',
                'kategori_id' => 1,
                'gudang_id' => 2,
                'stok' => 150,
                'harga_beli' => 2500,
                'harga_jual' => 4000,
                'satuan' => 'Pcs',
                'deskripsi' => null,
                'gambar' => null,
            ],
            [
                'id' => 3,
                'kode_barang' => 'LFB0001',
                'nama_barang' => 'Lifebuoy',
                'kategori_id' => 3,
                'gudang_id' => 3,
                'stok' => 200,
                'harga_beli' => 20000,
                'harga_jual' => 25000,
                'satuan' => 'Pack',
                'deskripsi' => null,
                'gambar' => null,
            ],
            [
                'id' => 4,
                'kode_barang' => 'AQU00002',
                'nama_barang' => 'Aqua Galon',
                'kategori_id' => 2,
                'gudang_id' => 2,
                'stok' => 100,
                'harga_beli' => 15000,
                'harga_jual' => 17000,
                'satuan' => 'Galon',
                'deskripsi' => null,
                'gambar' => null,
            ],
        ];

        foreach ($barangs as $barang) {
            DB::table('stok_barang')->updateOrInsert(
                ['kode_barang' => $barang['kode_barang']], // Cek by kode_barang
                array_merge($barang, [
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null,
                ])
            );
        }

        echo "✅ Stok Barang seeded successfully!\n";

        // ========== 5. SETTINGS ==========
        $settings = [
            ['id' => 1, 'key' => 'company_name', 'value' => 'Unit Usaha KMP Sawangan Baru'],
            ['id' => 2, 'key' => 'address', 'value' => 'Jl. H. Maksum No.4/3, Sawangan Baru, Kec. Sawangan, Kota Depok, Jawa Barat 16511'],
            ['id' => 3, 'key' => 'phone', 'value' => '085711321108'],
            ['id' => 4, 'key' => 'footer_text', 'value' => 'Terima kasih sudah berbelanja :)'],
            ['id' => 5, 'key' => 'member_discount', 'value' => '10'],
            ['id' => 6, 'key' => 'non_member_discount', 'value' => '0'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']], // Cek by key (unique)
                $setting
            );
        }

        echo "✅ Settings seeded successfully!\n";

        // ========== 6. ANGGOTA ==========
        DB::table('anggota')->updateOrInsert(
            ['kode_anggota' => 'KMPSB001030726'], // Cek by kode_anggota
            [
                'id_anggota' => 1,
                'nama_anggota' => 'Safa Nauval Nugraha',
                'email_anggota' => 'safanauval@gmail.com',
                'telepon_anggota' => '-',
                'alamat_anggota' => '-',
                'tanggal_masuk' => '2026-07-03',
                'created_at' => '2026-07-03 07:07:07',
                'updated_at' => '2026-07-03 07:07:07',
            ]
        );

        echo "✅ Anggota seeded successfully!\n";

        // ========== 7. SIMPANAN ==========
        DB::table('simpan')->updateOrInsert(
            ['id_anggota' => 1, 'jenis_simpanan' => 'pokok'], // Cek by id_anggota & jenis
            [
                'jumlah' => 100000,
                'payment_method' => 'tunai',
                'tanggal' => '2026-07-03',
                'total_simpanan' => 100000,
                'created_at' => '2026-07-03 07:07:07',
                'updated_at' => '2026-07-03 07:07:07',
            ]
        );

        echo "✅ Simpanan seeded successfully!\n";

        echo "══════════════════════════════════════\n";
        echo "  🚀 SEMUA DATA BERHASIL DI-SEED!  \n";
        echo "══════════════════════════════════════\n";
    }
}