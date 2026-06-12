<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage residences',
            'view residences',
            'manage lots',
            'manage accounting',
            'view accounting',
            'manage meetings',
            'vote in meetings',
            'report incidents',
            'manage incidents',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $syndicRole = Role::create(['name' => 'syndic']);
        $syndicRole->givePermissionTo(Permission::all());

        $proprietaireRole = Role::create(['name' => 'proprietaire']);
        $proprietaireRole->givePermissionTo([
            'view residences',
            'view accounting',
            'vote in meetings',
            'report incidents',
        ]);

        // Create a default Super Admin / Syndic
        $user = User::create([
            'name' => 'Admin SyndicPro',
            'email' => 'admin@syndicpro.ma',
            'password' => Hash::make('Admin@2026'),
            'is_active' => true,
        ]);
        $user->assignRole($syndicRole);
    }
}
