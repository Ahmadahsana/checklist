<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'nama_lengkap' => 'Administrator',
            'email' => 'admin@asrama.com',
            'no_hp' => '08123456789',
            'tempat_tanggal_lahir' => 'Jakarta, 1 Januari 2000',
            'alamat_asal' => 'Jl. Jalan No. 1',
            'prodi' => 'Teknik Informatika',
            'fakultas' => 'Teknik',
            'angkatan' => '2019',
            'nama_ayah' => 'Ayah',
            'nama_ibu' => 'Ibu',
            'no_hp_ortu' => '08123456789',
            'no_hp_saudara_wali' => '08123456789',
            'nama_kos' => 'Kosan',
            'harga_kos' => '500000',
        ]);
    }
}
