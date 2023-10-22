<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();


        $this->importSystemAdminRolesAndPermissions();

        $this->importGlobalAdminRolesAndPermissions();

//        $this->importCountryAdminRolesAdnPermissions();
//        $this->importNetworkManagerAdminRolesAdnPermissions();
//        $this->importFarmerHubAdminRolesAdnPermissions();
    }

    public function importSystemAdminRolesAndPermissions()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Insert Required Permissions
        $permissions = [
            'system_config',
            'system_up',
            'system_control',
            'system_down',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission); // This will create the permission if it does not exist
        }

        // Insert Required Role
        $roleName = 'SystemAdmin';
        $systemAdminRole = Role::findOrCreate($roleName); // This will create the role if it does not exist

        // Assign Permissions to the Role
        $systemAdminRole->syncPermissions($permissions); // This will assign the permissions to the role

        // Create system admin user if not exists and assign the role
        $userEmail = env('SYSTEM_ADMIN_EMAIL');
        $userPassword = bcrypt(env('SYSTEM_ADMIN_PASSWORD'));

        $systemAdminUser = User::firstOrCreate(
            ['email' => $userEmail],
            [
                'name' => 'System Admin',
                'password' => $userPassword
            ] // Ensure to bcrypt the password, consider using Hash::make() as an alternative
        );

        // Assign the role to the user
        if (!$systemAdminUser->hasRole($roleName)) {
            $systemAdminUser->assignRole($roleName); // If the user doesn't have the role, assign it
        }
    }

    public function importGlobalAdminRolesAndPermissions()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define the required permissions for GlobalAdmin
        $permissions = [
            'manage_country_user',
            'preview_country_admin_report',
            'preview_network_manager_report',
            'preview_farmers_hub_report',
            'global_admin_call_center_access',
        ];

        // Create the permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Define the role name
        $roleName = 'GlobalAdmin';

        // Create the role if it doesn't exist
        $globalAdminRole = \Spatie\Permission\Models\Role::findOrCreate($roleName);

        // Assign the permissions to the GlobalAdmin role
        $globalAdminRole->syncPermissions($permissions);

        // Get the global admin's email and password from the environment, or define default values
        $userEmail = env('GLOBAL_ADMIN_EMAIL', 'global-admin@example.com'); // fallback to default email if not in .env
        $userPassword = bcrypt(env('GLOBAL_ADMIN_PASSWORD')); // Consider a more secure default password or a different approach to handle this

        // Create the global admin user if they don't exist, and assign the GlobalAdmin role
        $globalAdminUser = \App\Models\User::firstOrCreate(
            ['email' => $userEmail],
            [
                'name' => 'Global Admin',
                'password' => $userPassword, // bcrypt the password or use Hash::make()
            ]
        );

        // Check if the user already has the role, if not, assign it
        if (!$globalAdminUser->hasRole($roleName)) {
            $globalAdminUser->assignRole($roleName);
        }
    }
}
