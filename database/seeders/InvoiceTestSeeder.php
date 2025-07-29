<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Spatie\Permission\Models\Role;

class InvoiceTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles si no existen
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $ventasRole = Role::firstOrCreate(['name' => 'Ventas']);
        $secretarioRole = Role::firstOrCreate(['name' => 'Secretario']);
        $bodegaRole = Role::firstOrCreate(['name' => 'Bodega']);

        // Crear usuarios de prueba
        $admin = User::firstOrCreate([
            'email' => 'admin@test.com'
        ], [
            'name' => 'Admin Test',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $admin->assignRole($adminRole);

        $vendedor = User::firstOrCreate([
            'email' => 'vendedor@test.com'
        ], [
            'name' => 'Vendedor Test',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $vendedor->assignRole($ventasRole);

        $secretario = User::firstOrCreate([
            'email' => 'secretario@test.com'
        ], [
            'name' => 'Secretario Test',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $secretario->assignRole($secretarioRole);

        // Crear cliente de prueba
        $client = Client::firstOrCreate([
            'email' => 'cliente@test.com'
        ], [
            'name' => 'Cliente Test',
            'document_type' => 'DNI',
            'document_number' => '12345678',
            'phone' => '987654321',
            'address' => 'Dirección de prueba 123',
        ]);

        // Crear productos de prueba
        $product1 = Product::firstOrCreate([
            'name' => 'Producto Test 1'
        ], [
            'description' => 'Descripción del producto 1',
            'price' => 100.00,
            'stock' => 50,
        ]);

        $product2 = Product::firstOrCreate([
            'name' => 'Producto Test 2'
        ], [
            'description' => 'Descripción del producto 2',
            'price' => 200.00,
            'stock' => 30,
        ]);

        // Crear facturas de prueba
        for ($i = 1; $i <= 3; $i++) {
            $invoice = Invoice::create([
                'invoice_number' => 'INV-TEST-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'client_id' => $client->id,
                'user_id' => $vendedor->id,
                'issue_date' => now()->subDays($i),
                'due_date' => now()->addDays(30 - $i),
                'subtotal' => 300.00,
                'tax' => 54.00,
                'total' => 354.00,
                'status' => 'active',
            ]);

            // Crear items de la factura
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $product1->id,
                'product_name' => $product1->name,
                'quantity' => 2,
                'unit_price' => 100.00,
                'total' => 200.00,
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $product2->id,
                'product_name' => $product2->name,
                'quantity' => 1,
                'unit_price' => 100.00,
                'total' => 100.00,
            ]);
        }

        $this->command->info('Datos de prueba para facturas creados exitosamente!');
    }
}
