<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@nuvex.ao',
            'phone' => '+244 900 000 001',
            'role' => 'superadmin',
            'active' => true,
            'password' => Hash::make('password'),
        ]);

        AdminUser::create([
            'user_id' => $admin->id,
            'department' => 'Administração',
            'position' => 'Super Administrador',
            'is_superadmin' => true,
            'permissions' => ['*'],
        ]);

        $support = User::create([
            'name' => 'Suporte NUVEX',
            'email' => 'suporte@nuvex.ao',
            'phone' => '+244 900 000 002',
            'role' => 'admin',
            'active' => true,
            'password' => Hash::make('password'),
        ]);

        AdminUser::create([
            'user_id' => $support->id,
            'department' => 'Suporte',
            'position' => 'Agente de Suporte',
            'is_superadmin' => false,
            'permissions' => ['tickets', 'services', 'dns'],
        ]);
    }
}
