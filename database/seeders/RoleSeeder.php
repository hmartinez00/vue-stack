<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_admin = Role::create(['name' => 'admin']);
        $role_client = Role::create(['name' => 'client']);

        // Permisos
            // administrative
        $permission_administrative = Permission::create(['name' => 'administrative']);
        // dashboard
        $permission_access_dashboard = Permission::create(['name' => 'dashboard']);

        // migrations
        $permission_migrations_index = Permission::create(['name' => 'migrations.index']);
        $permission_migrations_create = Permission::create(['name' => 'migrations.create']);
        $permission_migrations_edit = Permission::create(['name' => 'migrations.edit']);
        $permission_migrations_destroy = Permission::create(['name' => 'migrations.destroy']);

        //Permisos para el Administrador del Sistema
        $permissions_admin = [
            $permission_administrative,
            // dashboard
            $permission_access_dashboard ,

            // migrations
            $permission_migrations_index ,
            $permission_migrations_create ,
            $permission_migrations_edit ,
            $permission_migrations_destroy ,
        ];

        //Permisos para los clientes del Sistema
        $permissions_client = [];
        $excluded_permissions = [
            $permission_administrative,

            // migrations
            $permission_migrations_index ,
            $permission_migrations_create ,
            $permission_migrations_edit ,
            $permission_migrations_destroy ,

        ];

        foreach ($permissions_admin as $value) {
            if (!in_array($value, $excluded_permissions)) {
                $permissions_client[] = $value;
            }
        }

        //Le asignamos todos los permisos al Rol de Administrador
        $role_admin->syncPermissions($permissions_admin);

        //Le asignamos todos los permisos al Rol de client
        $role_client->syncPermissions($permissions_client);

    }
}
