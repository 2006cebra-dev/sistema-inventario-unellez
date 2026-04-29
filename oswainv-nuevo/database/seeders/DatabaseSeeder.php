<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@oswa.com',
            'password' => bcrypt('password'),
            'rol' => 'admin',
        ]);

        User::create([
            'name' => 'Empleado',
            'email' => 'empleado@oswa.com',
            'password' => bcrypt('password'),
            'rol' => 'empleado',
        ]);

        $productos = [
            ['codigo' => 'P001', 'nombre' => 'Aspirina 500mg', 'stock' => 150, 'marca' => 'Bayer', 'categoria' => 'Medicamentos', 'precio' => 12.50, 'fecha_vencimiento' => '2026-12-31'],
            ['codigo' => 'P002', 'nombre' => 'Ibuprofeno 400mg', 'stock' => 3, 'marca' => 'Genfar', 'categoria' => 'Medicamentos', 'precio' => 8.75, 'fecha_vencimiento' => '2026-08-15'],
            ['codigo' => 'P003', 'nombre' => 'Paracetamol 500mg', 'stock' => 8, 'marca' => 'Tecnoquimicas', 'categoria' => 'Medicamentos', 'precio' => 6.00, 'fecha_vencimiento' => '2026-06-20'],
            ['codigo' => 'P004', 'nombre' => 'Vitamina C 1000mg', 'stock' => 200, 'marca' => 'Centrum', 'categoria' => 'Vitaminas', 'precio' => 18.90, 'fecha_vencimiento' => '2027-03-10'],
            ['codigo' => 'P005', 'nombre' => ' Alcohol Antiséptico', 'stock' => 45, 'marca' => 'ProQuiz', 'categoria' => 'Cuidado Personal', 'precio' => 4.50, 'fecha_vencimiento' => '2027-01-15'],
            ['codigo' => 'P006', 'nombre' => 'Termómetro Digital', 'stock' => 12, 'marca' => 'Omron', 'categoria' => 'Equipos', 'precio' => 25.00, 'fecha_vencimiento' => null],
            ['codigo' => 'P007', 'nombre' => 'Gasa Estéril', 'stock' => 80, 'Marca' => 'Curad', 'categoria' => 'Cuidado Personal', 'precio' => 5.25, 'fecha_vencimiento' => '2027-06-30'],
            ['codigo' => 'P008', 'nombre' => 'Crema Hidratante', 'stock' => 5, 'marca' => 'Nivea', 'categoria' => 'Cuidado Personal', 'precio' => 9.99, 'fecha_vencimiento' => '2026-05-10'],
        ];

        foreach ($productos as $p) {
            \App\Models\Producto::create($p);
        }
    }
}