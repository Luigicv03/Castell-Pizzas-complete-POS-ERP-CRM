<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Table;

class CreateMissingTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear las mesas faltantes
        $missingTables = [
            ['name' => 'Mesa 13', 'capacity' => 4, 'status' => 'free'],
            ['name' => 'Mesa 14', 'capacity' => 2, 'status' => 'free'],
            ['name' => 'Mesa 15', 'capacity' => 2, 'status' => 'free'],
            ['name' => 'Mesa 16', 'capacity' => 4, 'status' => 'free'],
            ['name' => 'Mesa 17', 'capacity' => 2, 'status' => 'free'],
        ];

        foreach ($missingTables as $tableData) {
            $table = Table::firstOrCreate(
                ['name' => $tableData['name']],
                [
                    'capacity' => $tableData['capacity'],
                    'status' => $tableData['status'],
                    'is_active' => true,
                    'zone' => 'Sin Asignar', // Se asignará después
                ]
            );
            
            if ($table->wasRecentlyCreated) {
                echo "✓ Creada: {$table->name}\n";
            } else {
                echo "• Ya existía: {$table->name}\n";
            }
        }

        echo "\n✓ Mesas faltantes creadas correctamente\n";
    }
}