<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run()
    {
        $programs = [
            // Program yang sama untuk Regular dan Tahfidz (level 'both')
            ['nama_program' => 'Sholat Jammaah', 'target' => '3', 'unit' => 'kali', 'level' => 'both', 'type' => 'boolean'],
            ['nama_program' => 'Dzikir Pagi Al-Ma’surat', 'target' => '1', 'unit' => 'kali', 'level' => 'both', 'type' => 'boolean'],
            ['nama_program' => 'Dzikir Petang Al-Ma’surat', 'target' => '1', 'unit' => 'kali', 'level' => 'both', 'type' => 'boolean'],
            ['nama_program' => 'Tahajjud', 'target' => '1', 'unit' => 'kali', 'level' => 'both', 'type' => 'boolean'],
            ['nama_program' => 'Dhuha', 'target' => '1', 'unit' => 'kali', 'level' => 'both', 'type' => 'boolean'],
            ['nama_program' => 'Infaq', 'target' => '1', 'unit' => 'kali', 'level' => 'both', 'type' => 'boolean'],
            ['nama_program' => 'Puasa Sunnah', 'target' => '1', 'unit' => 'kali', 'level' => 'both', 'type' => 'boolean'],
            ['nama_program' => 'Membaca Al-Qur’an 1/2 halaman dan artinya', 'target' => '1', 'unit' => 'kali', 'level' => 'both', 'type' => 'boolean'],
            ['nama_program' => 'Membaca Riyadhus Shalihin 1/2 halaman', 'target' => '1', 'unit' => 'kali', 'level' => 'both', 'type' => 'boolean'],
            ['nama_program' => 'Membaca Surah Al-Kahfi', 'target' => '1', 'unit' => 'kali', 'level' => 'both', 'type' => 'boolean'],
            ['nama_program' => 'Membaca Surah Al-Mulk', 'target' => '1', 'unit' => 'kali', 'level' => 'both', 'type' => 'boolean'],
            ['nama_program' => 'Murojaah 1 surah juz 30', 'target' => '1', 'unit' => 'kali', 'level' => 'both', 'type' => 'boolean'],
            ['nama_program' => 'Mengikuti CBC (Character Building Class)', 'target' => '1', 'unit' => 'kali', 'level' => 'both', 'type' => 'boolean'],

            // Program Spesifik Regular
            ['nama_program' => 'Membaca Al-Qur’an Mandiri', 'target' => '10', 'unit' => 'halaman', 'level' => 'regular', 'type' => 'numeric'],
            ['nama_program' => 'Mengikuti SGD (Small Group Discussion)', 'target' => '1', 'unit' => 'kali', 'level' => 'regular', 'type' => 'boolean'],

            // Program Spesifik Tahfidz
            ['nama_program' => 'Shalat Rawatib', 'target' => '3', 'unit' => 'kali', 'level' => 'tahfidz', 'type' => 'boolean'],
            ['nama_program' => 'Membaca Al-Qur’an Mandiri', 'target' => '20', 'unit' => 'halaman', 'level' => 'tahfidz', 'type' => 'numeric'],
            ['nama_program' => 'Menghafal Al-Qur’an', 'target' => '1', 'unit' => 'halaman', 'level' => 'tahfidz', 'type' => 'numeric'],
            ['nama_program' => 'Murojaah', 'target' => '3', 'unit' => 'halaman', 'level' => 'tahfidz', 'type' => 'numeric'],
            ['nama_program' => 'Simpan Hafalan', 'target' => '5', 'unit' => 'halaman', 'level' => 'tahfidz', 'type' => 'numeric'],
            ['nama_program' => 'Mengikuti Majelis Tahsin Qiroati', 'target' => '1', 'unit' => 'kali', 'level' => 'tahfidz', 'type' => 'boolean'],
            ['nama_program' => 'Simpan Binnadzor', 'target' => '1', 'unit' => 'kali', 'level' => 'tahfidz', 'type' => 'boolean'],
            ['nama_program' => 'Mengikuti Majelis Tahsin Matan Jazari', 'target' => '1', 'unit' => 'kali', 'level' => 'tahfidz', 'type' => 'boolean'],
        ];

        Program::insert($programs);
    }
}
