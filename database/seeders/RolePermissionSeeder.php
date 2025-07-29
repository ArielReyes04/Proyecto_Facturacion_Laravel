<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create roles for the billing system
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $secretaryRole = Role::firstOrCreate(['name' => 'Secretario']);
        $warehouseRole = Role::firstOrCreate(['name' => 'Bodega']);
        $salesRole = Role::firstOrCreate(['name' => 'Ventas']);

        // Create permissions
        Permission::firstOrCreate(['name' => 'access.system']);
        Permission::firstOrCreate(['name' => 'manage.users']);
        Permission::firstOrCreate(['name' => 'manage.clients']);
        Permission::firstOrCreate(['name' => 'manage.products']);
        Permission::firstOrCreate(['name' => 'manage.invoices']);
        Permission::firstOrCreate(['name' => 'view.dashboard']);

        // Assign permissions to roles
        $adminRole->givePermissionTo(['access.system', 'manage.users', 'manage.clients', 'manage.products', 'manage.invoices', 'view.dashboard']);
        $secretaryRole->givePermissionTo(['access.system', 'manage.clients', 'view.dashboard']);
        $warehouseRole->givePermissionTo(['access.system', 'manage.products', 'view.dashboard']);
        $salesRole->givePermissionTo(['access.system', 'manage.invoices', 'view.dashboard']);

        // Create or update admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@facturacion.com'],
            [
                'name' => 'Administrador del Sistema',
                'password' => bcrypt('admin123'), // Use a secure password
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role
        $admin->assignRole('Administrador');

        // Create test users for each role
        $secretary = User::updateOrCreate(
            ['email' => 'secretario@facturacion.com'],
            [
                'name' => 'Usuario Secretario',
                'password' => bcrypt('secretario123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $secretary->assignRole('Secretario');

        $warehouse = User::updateOrCreate(
            ['email' => 'bodega@facturacion.com'],
            [
                'name' => 'Usuario Bodega',
                'password' => bcrypt('bodega123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $warehouse->assignRole('Bodega');

        $sales = User::updateOrCreate(
            ['email' => 'ventas@facturacion.com'],
            [
                'name' => 'Usuario Ventas',
                'password' => bcrypt('ventas123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $sales->assignRole('Ventas');
    }
}
