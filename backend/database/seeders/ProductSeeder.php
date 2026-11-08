<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Add a small catalog for the local Docker demonstration.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop Lenovo IdeaPad',
                'description' => 'Laptop para trabajo y estudio.',
                'price' => 699.99,
                'stock' => 10,
            ],
            [
                'name' => 'Mouse Logitech MX Master',
                'description' => 'Mouse inalámbrico ergonómico.',
                'price' => 89.99,
                'stock' => 25,
            ],
            [
                'name' => 'Teclado Mecánico Redragon',
                'description' => 'Teclado mecánico para gaming y productividad.',
                'price' => 59.99,
                'stock' => 15,
            ],
            [
                'name' => 'Monitor Samsung 24 pulgadas',
                'description' => 'Monitor Full HD de 24 pulgadas.',
                'price' => 179.99,
                'stock' => 8,
            ],
            [
                'name' => 'Audífonos Sony WH-1000XM5',
                'description' => 'Audífonos inalámbricos con cancelación de ruido.',
                'price' => 349.99,
                'stock' => 5,
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['name' => $product['name']],
                $product,
            );
        }
    }
}
