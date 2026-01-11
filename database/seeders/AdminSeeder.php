<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view orders',
            'edit orders',
            'delete orders',
            'view donations',
            'edit donations',
            'view volunteer applications',
            'edit volunteer applications',
            'view customers',
            'view newsletter subscriptions',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);

        // Assign all permissions to admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign specific permissions to manager
        $managerRole->givePermissionTo([
            'view products',
            'create products',
            'edit products',
            'view orders',
            'edit orders',
            'view donations',
            'edit donations',
            'view volunteer applications',
            'edit volunteer applications',
            'view customers',
        ]);

        // Assign view permissions to staff
        $staffRole->givePermissionTo([
            'view products',
            'view orders',
            'view donations',
            'view volunteer applications',
            'view customers',
        ]);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@scorebeyondleadership.org'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'), // Change this in production!
            ]
        );

        $admin->assignRole('admin');

        $this->command->info('Admin user created:');
        $this->command->info('Email: admin@scorebeyondleadership.org');
        $this->command->info('Password: password');
        $this->command->warn('Please change the password after first login!');
    }
}


