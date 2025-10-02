<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Table;

class TableZonesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Salón Principal: Mesa 1, Mesa 2 y 3, Mesa 4 y 5
        $salonPrincipal = ['Mesa 1', 'Mesa 2', 'Mesa 3', 'Mesa 4', 'Mesa 5'];
        
        // Salón Inferior: Mesa 6, Mesa 7, 8 y 9, Mesa 10, Mesa 11, Mesa 12 y 13
        $salonInferior = ['Mesa 6', 'Mesa 7', 'Mesa 8', 'Mesa 9', 'Mesa 10', 'Mesa 11', 'Mesa 12', 'Mesa 13'];
        
        // Terraza: Mesa 14, Mesa 15, Mesa 16, Mesa 17
        $terraza = ['Mesa 14', 'Mesa 15', 'Mesa 16', 'Mesa 17'];

        // Actualizar mesas del Salón Principal
        foreach ($salonPrincipal as $tableName) {
            Table::where('name', $tableName)->update(['zone' => 'Salón Principal']);
        }

        // Actualizar mesas del Salón Inferior
        foreach ($salonInferior as $tableName) {
            Table::where('name', $tableName)->update(['zone' => 'Salón Inferior']);
        }

        // Actualizar mesas de la Terraza
        foreach ($terraza as $tableName) {
            Table::where('name', $tableName)->update(['zone' => 'Terraza']);
        }

        echo "✓ Zonas asignadas a las mesas correctamente\n";
    }
}
