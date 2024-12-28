<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Define the required permissions for admin and user roles
        $adminPermissions = [
            'show_permissions',
            'manage_permissions',
            'reserve',
            'list_books',
            'manage_books',
            'delete_books'
        ];

        $userPermissions = [
            'reserve',
            'list_books'
        ];

        // Create permissions if they don't exist
        foreach (array_merge($adminPermissions, $userPermissions) as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Ensure the admin role exists or create it
        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
            $adminRole = Role::create(['name' => 'admin']);
        }

        // Create user role
        $userRole = Role::where('name', 'user')->first();
        if (!$userRole) {
            $userRole = Role::create(['name' => 'user']);
        }

        // Assign the appropriate permissions to the roles
        foreach ($adminPermissions as $permission) {
            $adminRole->givePermissionTo($permission);
        }

        foreach ($userPermissions as $permission) {
            $userRole->givePermissionTo($permission);
        }

        // Create the admin user if not already exists
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
            ]
        );

        // Assign the "admin" role to the admin user
        $adminUser->assignRole($adminRole);

        // Optionally, create a sample user and assign the "user" role
        $sampleUser = User::firstOrCreate(
            ['email' => 'user@user.com'],
            [
                'name' => 'Sample User',
                'password' => bcrypt('password'),
            ]
        );

        // Assign the "user" role to the sample user
        $sampleUser->assignRole($userRole);
    }
}
