<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    protected $signature = 'make:superadmin {email} {password}';
    protected $description = 'Crea un usuario Super Admin';

    public function handle(): void
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($password),
            ]
        );

        if (!$user->hasRole($role->name)) {
            $user->assignRole($role);
        }

        $this->info("✅ Super Admin creado: {$email}");
    }
}
