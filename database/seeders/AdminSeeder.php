<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Profile;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(
            ['id' => 2],
            ['name' => 'admin']
        );
        $profile = Profile::firstOrCreate([
            'bio' => 'administrator',
        ], [
            'alamat' => '-',
            'umur' => 0,
        ]);

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'role_id' => $role->id,
                'profile_id' => $profile->id,
            ]
        );
    }
}
