<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Product;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear clientes de prueba
        Client::create([
            'name' => 'Juan Pérez García',
            'email' => 'juan.perez@email.com',
            'phone' => '987654321',
            'address' => 'Av. Los Olivos 123, San Juan de Lurigancho',
            'document_type' => 'DNI',
            'document_number' => '12345678',
            'is_active' => true,
        ]);

        Client::create([
            'name' => 'María López Fernández',
            'email' => 'maria.lopez@email.com',
            'phone' => '987654322',
            'address' => 'Jr. Las Flores 456, Miraflores',
            'document_type' => 'DNI',
            'document_number' => '23456789',
            'is_active' => true,
        ]);

        Client::create([
            'name' => 'Empresa ABC S.A.C.',
            'email' => 'contacto@empresaabc.com',
            'phone' => '01-234-5678',
            'address' => 'Av. Javier Prado 789, San Isidro',
            'document_type' => 'RUC',
            'document_number' => '20123456789',
            'is_active' => true,
        ]);

        // Crear productos de prueba
        Product::create([
            'name' => 'Laptop HP Pavilion 15',
            'description' => 'Laptop HP Pavilion 15 pulgadas, Intel Core i5, 8GB RAM, 512GB SSD',
            'price' => 2599.99,
            'stock' => 15,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Mouse Inalámbrico Logitech',
            'description' => 'Mouse inalámbrico Logitech MX Master 3, ergonómico y de alta precisión',
            'price' => 129.99,
            'stock' => 50,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Teclado Mecánico Gaming',
            'description' => 'Teclado mecánico gaming con retroiluminación RGB, switches azules',
            'price' => 189.99,
            'stock' => 25,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Monitor 24" Full HD',
            'description' => 'Monitor LED 24 pulgadas Full HD 1920x1080, 75Hz, HDMI/VGA',
            'price' => 399.99,
            'stock' => 8,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Auriculares Bluetooth Sony',
            'description' => 'Auriculares Sony WH-1000XM4, cancelación de ruido, 30h de batería',
            'price' => 549.99,
            'stock' => 12,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Webcam HD Logitech',
            'description' => 'Webcam Logitech C920 HD 1080p, micrófono integrado, USB',
            'price' => 159.99,
            'stock' => 30,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Impresora Multifuncional HP',
            'description' => 'Impresora HP DeskJet 3775, imprime, copia, escanea, WiFi',
            'price' => 249.99,
            'stock' => 5,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Disco Duro Externo 1TB',
            'description' => 'Disco duro externo portátil 1TB, USB 3.0, compatible con PC y Mac',
            'price' => 199.99,
            'stock' => 20,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Cable HDMI 2.0',
            'description' => 'Cable HDMI 2.0 de 2 metros, soporte 4K, alta velocidad',
            'price' => 29.99,
            'stock' => 3,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Adaptador USB-C Hub',
            'description' => 'Hub USB-C 7 en 1, HDMI, USB 3.0, lector SD, carga rápida',
            'price' => 89.99,
            'stock' => 7,
            'is_active' => true,
        ]);
    }
}
