<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Buat user pegawai
        $userPegawai = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@test.com',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
        ]);

        // Buat employee dan tautkan ke user
        Employee::create([
            'user_id' => $userPegawai->id,
            'nama' => 'Budi Santoso',
            'email' => 'budi@test.com',
            'NIP' => '123456789',
            'jabatan' => 'Staff',
            'unit_kerja' => 'IT',
            'tanggal_bergabung' => now(),
            'sisa_cuti_tahunan' => 12,
        ]);
    }
}
