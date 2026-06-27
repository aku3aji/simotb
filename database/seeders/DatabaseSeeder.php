<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(MasterDataSeeder::class);

        User::updateOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Owner',
                'role' => User::ROLE_OWNER,
                'pegawai_id' => Pegawai::where('nama', 'Owner Sumber Alam')->firstOrFail()->id,
                'is_active' => true,
                'password' => 'password',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'role' => User::ROLE_ADMIN,
                'pegawai_id' => Pegawai::where('nama', 'Admin Kasir')->firstOrFail()->id,
                'is_active' => true,
                'password' => 'password',
            ]
        );

        $this->call(TransactionSeeder::class);
    }
}
