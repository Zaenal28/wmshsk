<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            // 1. Super Admin (Khusus Kelola Akun Pengguna)
            [
                'name' => 'Super Admin (Kelola Akun)',
                'email' => 'superadmin@wms.com',
                'password' => Hash::make('hsksecret0208'),
                'role' => 'superadmin',
                'cabang' => 'Super Admin',
                'pic' => 'Super Admin',
                'is_active' => true,
            ],
            // 2. Gudang Pusat (Khusus Portal Filament Admin /admin)
            [
                'name' => 'Admin Gudang Pusat',
                'email' => 'gudangpusat@wms.com',
                'password' => Hash::make('password123'),
                'role' => 'gudang_pusat',
                'cabang' => 'Gudang Pusat',
                'pic' => 'Admin Gudang Pusat',
                'is_active' => true,
            ],
            // 2. Ruang Dokter Cianjur
            [
                'name' => 'Ruang Dokter Cianjur',
                'email' => 'ruangdoktercianjur@wms.com',
                'password' => Hash::make('hsksecret0208'),
                'role' => 'ruang_dokter',
                'cabang' => 'Ruang Dokter Cianjur',
                'pic' => 'Dokter Cianjur',
                'is_active' => true,
            ],
            // 3. Ruang Dokter Purwakarta
            [
                'name' => 'Ruang Dokter Purwakarta',
                'email' => 'ruangdokterpwk@wms.com',
                'password' => Hash::make('hsksecret0208'),
                'role' => 'ruang_dokter',
                'cabang' => 'Ruang Dokter Purwakarta',
                'pic' => 'Dokter Purwakarta',
                'is_active' => true,
            ],
            // 4. Ruang Dokter Bandung
            [
                'name' => 'Ruang Dokter Bandung',
                'email' => 'ruangdokterBandung@wms.com',
                'password' => Hash::make('hsksecret0208'),
                'role' => 'ruang_dokter',
                'cabang' => 'Ruang Dokter Bandung',
                'pic' => 'Dokter Bandung',
                'is_active' => true,
            ],
            // 5. Gudang Bandung
            [
                'name' => 'Gudang Bandung',
                'email' => 'gudangbandung@wms.com',
                'password' => Hash::make('hsksecret0208'),
                'role' => 'gudang_cabang',
                'cabang' => 'Gudang Bandung',
                'pic' => 'Admin Gudang Bandung',
                'is_active' => true,
            ],
            // 6. Gudang Purwakarta
            [
                'name' => 'Gudang Purwakarta',
                'email' => 'gudangpwk@wms.com',
                'password' => Hash::make('hsksecret0208'),
                'role' => 'gudang_cabang',
                'cabang' => 'Gudang Purwakarta',
                'pic' => 'Admin Gudang Purwakarta',
                'is_active' => true,
            ],
            // 7. Apotek Cianjur
            [
                'name' => 'Apotek Cianjur',
                'email' => 'apotekcianjur@wms.com',
                'password' => Hash::make('hsksecret0208'),
                'role' => 'apotek',
                'cabang' => 'Apotek Cianjur',
                'pic' => 'Apoteker Cianjur',
                'is_active' => true,
            ],
            // 8. Apotek Purwakarta
            [
                'name' => 'Apotek Purwakarta',
                'email' => 'apotekpwk@wms.com',
                'password' => Hash::make('hsksecret0208'),
                'role' => 'apotek',
                'cabang' => 'Apotek Purwakarta',
                'pic' => 'Apoteker Purwakarta',
                'is_active' => true,
            ],
            // 9. Apotek Bandung
            [
                'name' => 'Apotek Bandung',
                'email' => 'apotekbandung@wms.com',
                'password' => Hash::make('hsksecret0208'),
                'role' => 'apotek',
                'cabang' => 'Apotek Bandung',
                'pic' => 'Apoteker Bandung',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
