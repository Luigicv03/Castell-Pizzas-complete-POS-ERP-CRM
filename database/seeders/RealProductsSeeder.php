<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class RealProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar productos existentes
        Product::truncate();
        
        // Crear/obtener categorías
        $categories = [
            'Pizzas Tradicionales' => Category::firstOrCreate(['name' => 'Pizzas Tradicionales'], [
                'description' => 'Pizzas clásicas tradicionales',
                'color' => '#FF6B6B',
                'sort_order' => 1,
                'is_active' => true,
            ]),
            'Pizzas Especiales' => Category::firstOrCreate(['name' => 'Pizzas Especiales'], [
                'description' => 'Pizzas especiales de la casa',
                'color' => '#4ECDC4',
                'sort_order' => 2,
                'is_active' => true,
            ]),
            'Pizza Dolce' => Category::firstOrCreate(['name' => 'Pizza Dolce'], [
                'description' => 'Pizzas dulces',
                'color' => '#FFE66D',
                'sort_order' => 3,
                'is_active' => true,
            ]),
            'Pizza Multicereal' => Category::firstOrCreate(['name' => 'Pizza Multicereal'], [
                'description' => 'Pizzas con masa multicereal',
                'color' => '#A8E6CF',
                'sort_order' => 4,
                'is_active' => true,
            ]),
            'Calzone' => Category::firstOrCreate(['name' => 'Calzone'], [
                'description' => 'Calzones',
                'color' => '#FFB6C1',
                'sort_order' => 5,
                'is_active' => true,
            ]),
            'Pastichos' => Category::firstOrCreate(['name' => 'Pastichos'], [
                'description' => 'Pastichos',
                'color' => '#DDA0DD',
                'sort_order' => 6,
                'is_active' => true,
            ]),
            'Ingredientes' => Category::firstOrCreate(['name' => 'Ingredientes'], [
                'description' => 'Ingredientes adicionales',
                'color' => '#98FB98',
                'sort_order' => 7,
                'is_active' => true,
            ]),
            'Ingredientes Premium' => Category::firstOrCreate(['name' => 'Ingredientes Premium'], [
                'description' => 'Ingredientes premium',
                'color' => '#F0E68C',
                'sort_order' => 8,
                'is_active' => true,
            ]),
            'Cajas de Pizza' => Category::firstOrCreate(['name' => 'Cajas de Pizza'], [
                'description' => 'Cajas para delivery',
                'color' => '#87CEEB',
                'sort_order' => 9,
                'is_active' => true,
            ]),
        ];

        // PIZZAS TRADICIONALES
        $pizzasTradicionales = [
            [
                'name' => 'Pizza Margherita Personal 25cm',
                'description' => 'Salsa de tomate, mozzarella, albahaca',
                'price' => 5.00,
                'cost' => 1.471,
                'category_id' => $categories['Pizzas Tradicionales']->id,
                'sku' => 'PIZ-MARG-P',
                'preparation_time' => 15,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pizza Margherita Mediana 33cm',
                'description' => 'Salsa de tomate, mozzarella, albahaca',
                'price' => 7.50,
                'cost' => 2.2012,
                'category_id' => $categories['Pizzas Tradicionales']->id,
                'sku' => 'PIZ-MARG-M',
                'preparation_time' => 15,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pizza Margherita Familiar 40cm',
                'description' => 'Salsa de tomate, mozzarella, albahaca',
                'price' => 10.00,
                'cost' => 2.97,
                'category_id' => $categories['Pizzas Tradicionales']->id,
                'sku' => 'PIZ-MARG-F',
                'preparation_time' => 15,
                'sort_order' => 3,
            ],
            [
                'name' => 'Pizza Jamón Personal 25cm',
                'description' => 'Salsa de tomate, mozzarella, jamón',
                'price' => 6.00,
                'cost' => 1.736,
                'category_id' => $categories['Pizzas Tradicionales']->id,
                'sku' => 'PIZ-JAM-P',
                'preparation_time' => 15,
                'sort_order' => 4,
            ],
            [
                'name' => 'Pizza Jamón Mediana 33cm',
                'description' => 'Salsa de tomate, mozzarella, jamón',
                'price' => 9.00,
                'cost' => 2.5722,
                'category_id' => $categories['Pizzas Tradicionales']->id,
                'sku' => 'PIZ-JAM-M',
                'preparation_time' => 15,
                'sort_order' => 5,
            ],
            [
                'name' => 'Pizza Jamón Familiar 40cm',
                'description' => 'Salsa de tomate, mozzarella, jamón',
                'price' => 12.00,
                'cost' => 3.447,
                'category_id' => $categories['Pizzas Tradicionales']->id,
                'sku' => 'PIZ-JAM-F',
                'preparation_time' => 15,
                'sort_order' => 6,
            ],
            [
                'name' => 'Pizza Tocineta y Maíz Personal 25cm',
                'description' => 'Salsa de tomate, mozzarella, tocineta, maíz',
                'price' => 7.50,
                'cost' => 2.057,
                'category_id' => $categories['Pizzas Tradicionales']->id,
                'sku' => 'PIZ-TOMA-P',
                'preparation_time' => 15,
                'sort_order' => 7,
            ],
            [
                'name' => 'Pizza Tocineta y Maíz Mediana 33cm',
                'description' => 'Salsa de tomate, mozzarella, tocineta, maíz',
                'price' => 11.00,
                'cost' => 2.9812,
                'category_id' => $categories['Pizzas Tradicionales']->id,
                'sku' => 'PIZ-TOMA-M',
                'preparation_time' => 15,
                'sort_order' => 8,
            ],
            [
                'name' => 'Pizza Tocineta y Maíz Familiar 40cm',
                'description' => 'Salsa de tomate, mozzarella, tocineta, maíz',
                'price' => 15.00,
                'cost' => 4.106,
                'category_id' => $categories['Pizzas Tradicionales']->id,
                'sku' => 'PIZ-TOMA-F',
                'preparation_time' => 15,
                'sort_order' => 9,
            ],
            [
                'name' => 'Pizza 4 Estaciones Personal 25cm',
                'description' => 'Salsa de tomate, mozzarella, pepperoni, jamón, pimientos, cebolla',
                'price' => 10.00,
                'cost' => 2.9007,
                'category_id' => $categories['Pizzas Tradicionales']->id,
                'sku' => 'PIZ-4EST-P',
                'preparation_time' => 18,
                'sort_order' => 10,
            ],
            [
                'name' => 'Pizza 4 Estaciones Mediana 33cm',
                'description' => 'Salsa de tomate, mozzarella, pepperoni, jamón, pimientos, cebolla',
                'price' => 14.50,
                'cost' => 4.2137,
                'category_id' => $categories['Pizzas Tradicionales']->id,
                'sku' => 'PIZ-4EST-M',
                'preparation_time' => 18,
                'sort_order' => 11,
            ],
            [
                'name' => 'Pizza 4 Estaciones Familiar 40cm',
                'description' => 'Salsa de tomate, mozzarella, pepperoni, jamón, pimientos, cebolla',
                'price' => 20.00,
                'cost' => 5.958,
                'category_id' => $categories['Pizzas Tradicionales']->id,
                'sku' => 'PIZ-4EST-F',
                'preparation_time' => 18,
                'sort_order' => 12,
            ],
        ];

        // PIZZAS ESPECIALES
        $pizzasEspeciales = [
            [
                'name' => 'Pizza Di Abruzzo Personal 25cm',
                'description' => 'Pizza especial Di Abruzzo',
                'price' => 8.00,
                'cost' => 1.8483,
                'category_id' => $categories['Pizzas Especiales']->id,
                'sku' => 'PIZ-ABRU-P',
                'preparation_time' => 18,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pizza Di Abruzzo Mediana 33cm',
                'description' => 'Pizza especial Di Abruzzo',
                'price' => 10.50,
                'cost' => 2.6838,
                'category_id' => $categories['Pizzas Especiales']->id,
                'sku' => 'PIZ-ABRU-M',
                'preparation_time' => 18,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pizza Di Abruzzo Familiar 40cm',
                'description' => 'Pizza especial Di Abruzzo',
                'price' => 16.00,
                'cost' => 3.7164,
                'category_id' => $categories['Pizzas Especiales']->id,
                'sku' => 'PIZ-ABRU-F',
                'preparation_time' => 18,
                'sort_order' => 3,
            ],
            [
                'name' => 'Pizza Castell 1 Personal 25cm',
                'description' => 'Pizza especial Castell 1',
                'price' => 11.00,
                'cost' => 3.0433,
                'category_id' => $categories['Pizzas Especiales']->id,
                'sku' => 'PIZ-CAS1-P',
                'preparation_time' => 20,
                'sort_order' => 4,
            ],
            [
                'name' => 'Pizza Castell 1 Mediana 33cm',
                'description' => 'Pizza especial Castell 1',
                'price' => 16.00,
                'cost' => 4.5538,
                'category_id' => $categories['Pizzas Especiales']->id,
                'sku' => 'PIZ-CAS1-M',
                'preparation_time' => 20,
                'sort_order' => 5,
            ],
            [
                'name' => 'Pizza Castell 1 Familiar 40cm',
                'description' => 'Pizza especial Castell 1',
                'price' => 22.00,
                'cost' => 6.1064,
                'category_id' => $categories['Pizzas Especiales']->id,
                'sku' => 'PIZ-CAS1-F',
                'preparation_time' => 20,
                'sort_order' => 6,
            ],
            [
                'name' => 'Pizza Castell 2 Personal 25cm',
                'description' => 'Pizza especial Castell 2',
                'price' => 12.00,
                'cost' => 3.5158,
                'category_id' => $categories['Pizzas Especiales']->id,
                'sku' => 'PIZ-CAS2-P',
                'preparation_time' => 20,
                'sort_order' => 7,
            ],
            [
                'name' => 'Pizza Castell 2 Mediana 33cm',
                'description' => 'Pizza especial Castell 2',
                'price' => 17.00,
                'cost' => 4.6167,
                'category_id' => $categories['Pizzas Especiales']->id,
                'sku' => 'PIZ-CAS2-M',
                'preparation_time' => 20,
                'sort_order' => 8,
            ],
            [
                'name' => 'Pizza Castell 2 Familiar 40cm',
                'description' => 'Pizza especial Castell 2',
                'price' => 24.00,
                'cost' => 5.705,
                'category_id' => $categories['Pizzas Especiales']->id,
                'sku' => 'PIZ-CAS2-F',
                'preparation_time' => 20,
                'sort_order' => 9,
            ],
        ];

        // PIZZAS DOLCE
        $pizzasDolce = [
            [
                'name' => 'Pizza Dolce Personal 20cm',
                'description' => 'Pizza dulce especial',
                'price' => 5.50,
                'cost' => 3.919,
                'category_id' => $categories['Pizza Dolce']->id,
                'sku' => 'PIZ-DOLCE-P',
                'preparation_time' => 15,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pizza Dolce Pistacho Personal 20cm',
                'description' => 'Pizza dulce con pistacho',
                'price' => 8.00,
                'cost' => 3.919,
                'category_id' => $categories['Pizza Dolce']->id,
                'sku' => 'PIZ-PIST-P',
                'preparation_time' => 15,
                'sort_order' => 2,
            ],
        ];

        // PIZZAS MULTICEREAL
        $pizzasMulticereal = [
            [
                'name' => 'Pizza Multicereal Personal 25cm',
                'description' => 'Pizza con masa multicereal',
                'price' => 12.00,
                'cost' => 3.3674,
                'category_id' => $categories['Pizza Multicereal']->id,
                'sku' => 'PIZ-MULTI-P',
                'preparation_time' => 20,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pizza Multicereal Mediana 33cm',
                'description' => 'Pizza con masa multicereal',
                'price' => 17.00,
                'cost' => 5.1955,
                'category_id' => $categories['Pizza Multicereal']->id,
                'sku' => 'PIZ-MULTI-M',
                'preparation_time' => 20,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pizza Multicereal Familiar 40cm',
                'description' => 'Pizza con masa multicereal',
                'price' => 24.00,
                'cost' => 6.7627,
                'category_id' => $categories['Pizza Multicereal']->id,
                'sku' => 'PIZ-MULTI-F',
                'preparation_time' => 20,
                'sort_order' => 3,
            ],
        ];

        // CALZONES
        $calzones = [
            [
                'name' => 'Calzone Comer Aquí',
                'description' => 'Calzone para comer en el local',
                'price' => 4.50,
                'cost' => 2.00,
                'category_id' => $categories['Calzone']->id,
                'sku' => 'CAL-AQUI',
                'preparation_time' => 15,
                'sort_order' => 1,
            ],
            [
                'name' => 'Calzone Para Llevar',
                'description' => 'Calzone para llevar',
                'price' => 5.00,
                'cost' => 2.20,
                'category_id' => $categories['Calzone']->id,
                'sku' => 'CAL-LLEVAR',
                'preparation_time' => 15,
                'sort_order' => 2,
            ],
        ];

        // PASTICHOS
        $pastichos = [
            [
                'name' => 'Pasticho Tradicional Comer Aquí',
                'description' => 'Pasticho tradicional para comer en el local',
                'price' => 7.00,
                'cost' => 3.50,
                'category_id' => $categories['Pastichos']->id,
                'sku' => 'PAS-TRAD-A',
                'preparation_time' => 25,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pasticho Tradicional Para Llevar',
                'description' => 'Pasticho tradicional para llevar',
                'price' => 7.50,
                'cost' => 3.75,
                'category_id' => $categories['Pastichos']->id,
                'sku' => 'PAS-TRAD-L',
                'preparation_time' => 25,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pasticho Berenjena/Plátano/Zucchini Comer Aquí',
                'description' => 'Pasticho de berenjena, plátano o zucchini para comer en el local',
                'price' => 7.00,
                'cost' => 3.50,
                'category_id' => $categories['Pastichos']->id,
                'sku' => 'PAS-VEG-A',
                'preparation_time' => 25,
                'sort_order' => 3,
            ],
            [
                'name' => 'Pasticho Berenjena/Plátano/Zucchini Para Llevar',
                'description' => 'Pasticho de berenjena, plátano o zucchini para llevar',
                'price' => 7.50,
                'cost' => 3.75,
                'category_id' => $categories['Pastichos']->id,
                'sku' => 'PAS-VEG-L',
                'preparation_time' => 25,
                'sort_order' => 4,
            ],
        ];

        // INGREDIENTES BÁSICOS
        $ingredientes = [
            [
                'name' => 'Jamón Personal',
                'description' => 'Jamón extra para pizza personal',
                'price' => 1.00,
                'cost' => 0.265,
                'category_id' => $categories['Ingredientes']->id,
                'sku' => 'ING-JAM-P',
                'preparation_time' => 0,
                'sort_order' => 1,
            ],
            [
                'name' => 'Jamón Personal Doble',
                'description' => 'Jamón extra doble para pizza personal',
                'price' => 1.50,
                'cost' => 0.371,
                'category_id' => $categories['Ingredientes']->id,
                'sku' => 'ING-JAM-PD',
                'preparation_time' => 0,
                'sort_order' => 2,
            ],
            [
                'name' => 'Jamón Mediana',
                'description' => 'Jamón extra para pizza mediana',
                'price' => 1.50,
                'cost' => 0.371,
                'category_id' => $categories['Ingredientes']->id,
                'sku' => 'ING-JAM-M',
                'preparation_time' => 0,
                'sort_order' => 3,
            ],
            [
                'name' => 'Jamón Mediana Doble',
                'description' => 'Jamón extra doble para pizza mediana',
                'price' => 2.00,
                'cost' => 0.477,
                'category_id' => $categories['Ingredientes']->id,
                'sku' => 'ING-JAM-MD',
                'preparation_time' => 0,
                'sort_order' => 4,
            ],
            [
                'name' => 'Jamón Familiar',
                'description' => 'Jamón extra para pizza familiar',
                'price' => 2.00,
                'cost' => 0.477,
                'category_id' => $categories['Ingredientes']->id,
                'sku' => 'ING-JAM-F',
                'preparation_time' => 0,
                'sort_order' => 5,
            ],
            [
                'name' => 'Jamón Familiar Doble',
                'description' => 'Jamón extra doble para pizza familiar',
                'price' => 2.50,
                'cost' => 0.636,
                'category_id' => $categories['Ingredientes']->id,
                'sku' => 'ING-JAM-FD',
                'preparation_time' => 0,
                'sort_order' => 6,
            ],
            [
                'name' => 'Queso Personal',
                'description' => 'Queso extra para pizza personal',
                'price' => 1.00,
                'cost' => 1.185,
                'category_id' => $categories['Ingredientes']->id,
                'sku' => 'ING-QUE-P',
                'preparation_time' => 0,
                'sort_order' => 7,
            ],
            [
                'name' => 'Queso Personal Doble',
                'description' => 'Queso extra doble para pizza personal',
                'price' => 1.50,
                'cost' => 1.66,
                'category_id' => $categories['Ingredientes']->id,
                'sku' => 'ING-QUE-PD',
                'preparation_time' => 0,
                'sort_order' => 8,
            ],
            [
                'name' => 'Queso Mediana',
                'description' => 'Queso extra para pizza mediana',
                'price' => 1.50,
                'cost' => 1.66,
                'category_id' => $categories['Ingredientes']->id,
                'sku' => 'ING-QUE-M',
                'preparation_time' => 0,
                'sort_order' => 9,
            ],
            [
                'name' => 'Queso Mediana Doble',
                'description' => 'Queso extra doble para pizza mediana',
                'price' => 2.00,
                'cost' => 2.13,
                'category_id' => $categories['Ingredientes']->id,
                'sku' => 'ING-QUE-MD',
                'preparation_time' => 0,
                'sort_order' => 10,
            ],
            [
                'name' => 'Queso Familiar',
                'description' => 'Queso extra para pizza familiar',
                'price' => 2.00,
                'cost' => 2.13,
                'category_id' => $categories['Ingredientes']->id,
                'sku' => 'ING-QUE-F',
                'preparation_time' => 0,
                'sort_order' => 11,
            ],
            [
                'name' => 'Queso Familiar Doble',
                'description' => 'Queso extra doble para pizza familiar',
                'price' => 2.50,
                'cost' => 2.71,
                'category_id' => $categories['Ingredientes']->id,
                'sku' => 'ING-QUE-FD',
                'preparation_time' => 0,
                'sort_order' => 12,
            ],
        ];

        // INGREDIENTES PREMIUM
        $ingredientesPremium = [
            [
                'name' => 'Salami Personal',
                'description' => 'Salami premium para pizza personal',
                'price' => 1.50,
                'cost' => 0.38,
                'category_id' => $categories['Ingredientes Premium']->id,
                'sku' => 'ING-SAL-P',
                'preparation_time' => 0,
                'sort_order' => 1,
            ],
            [
                'name' => 'Salami Personal Doble',
                'description' => 'Salami premium doble para pizza personal',
                'price' => 2.00,
                'cost' => 0.53,
                'category_id' => $categories['Ingredientes Premium']->id,
                'sku' => 'ING-SAL-PD',
                'preparation_time' => 0,
                'sort_order' => 2,
            ],
            [
                'name' => 'Salami Mediana',
                'description' => 'Salami premium para pizza mediana',
                'price' => 2.00,
                'cost' => 0.53,
                'category_id' => $categories['Ingredientes Premium']->id,
                'sku' => 'ING-SAL-M',
                'preparation_time' => 0,
                'sort_order' => 3,
            ],
            [
                'name' => 'Salami Mediana Doble',
                'description' => 'Salami premium doble para pizza mediana',
                'price' => 2.50,
                'cost' => 0.68,
                'category_id' => $categories['Ingredientes Premium']->id,
                'sku' => 'ING-SAL-MD',
                'preparation_time' => 0,
                'sort_order' => 4,
            ],
            [
                'name' => 'Salami Familiar',
                'description' => 'Salami premium para pizza familiar',
                'price' => 2.50,
                'cost' => 0.68,
                'category_id' => $categories['Ingredientes Premium']->id,
                'sku' => 'ING-SAL-F',
                'preparation_time' => 0,
                'sort_order' => 5,
            ],
            [
                'name' => 'Salami Familiar Doble',
                'description' => 'Salami premium doble para pizza familiar',
                'price' => 3.00,
                'cost' => 0.95,
                'category_id' => $categories['Ingredientes Premium']->id,
                'sku' => 'ING-SAL-FD',
                'preparation_time' => 0,
                'sort_order' => 6,
            ],
            [
                'name' => 'Tocineta Personal',
                'description' => 'Tocineta premium para pizza personal',
                'price' => 1.50,
                'cost' => 0.32,
                'category_id' => $categories['Ingredientes Premium']->id,
                'sku' => 'ING-TOC-P',
                'preparation_time' => 0,
                'sort_order' => 7,
            ],
            [
                'name' => 'Tocineta Personal Doble',
                'description' => 'Tocineta premium doble para pizza personal',
                'price' => 2.00,
                'cost' => 0.4,
                'category_id' => $categories['Ingredientes Premium']->id,
                'sku' => 'ING-TOC-PD',
                'preparation_time' => 0,
                'sort_order' => 8,
            ],
            [
                'name' => 'Tocineta Mediana',
                'description' => 'Tocineta premium para pizza mediana',
                'price' => 2.00,
                'cost' => 0.4,
                'category_id' => $categories['Ingredientes Premium']->id,
                'sku' => 'ING-TOC-M',
                'preparation_time' => 0,
                'sort_order' => 9,
            ],
            [
                'name' => 'Tocineta Mediana Doble',
                'description' => 'Tocineta premium doble para pizza mediana',
                'price' => 2.50,
                'cost' => 0.68,
                'category_id' => $categories['Ingredientes Premium']->id,
                'sku' => 'ING-TOC-MD',
                'preparation_time' => 0,
                'sort_order' => 10,
            ],
            [
                'name' => 'Tocineta Familiar',
                'description' => 'Tocineta premium para pizza familiar',
                'price' => 2.50,
                'cost' => 0.68,
                'category_id' => $categories['Ingredientes Premium']->id,
                'sku' => 'ING-TOC-F',
                'preparation_time' => 0,
                'sort_order' => 11,
            ],
            [
                'name' => 'Tocineta Familiar Doble',
                'description' => 'Tocineta premium doble para pizza familiar',
                'price' => 3.00,
                'cost' => 0.8,
                'category_id' => $categories['Ingredientes Premium']->id,
                'sku' => 'ING-TOC-FD',
                'preparation_time' => 0,
                'sort_order' => 12,
            ],
        ];

        // CAJAS DE PIZZA
        $cajas = [
            [
                'name' => 'Caja Personal 25cm',
                'description' => 'Caja para pizza personal 25cm',
                'price' => 0.50,
                'cost' => 0.20,
                'category_id' => $categories['Cajas de Pizza']->id,
                'sku' => 'CAJ-P',
                'preparation_time' => 0,
                'sort_order' => 1,
            ],
            [
                'name' => 'Caja Mediana 33cm',
                'description' => 'Caja para pizza mediana 33cm',
                'price' => 0.70,
                'cost' => 0.28,
                'category_id' => $categories['Cajas de Pizza']->id,
                'sku' => 'CAJ-M',
                'preparation_time' => 0,
                'sort_order' => 2,
            ],
            [
                'name' => 'Caja Familiar 40cm',
                'description' => 'Caja para pizza familiar 40cm',
                'price' => 0.90,
                'cost' => 0.36,
                'category_id' => $categories['Cajas de Pizza']->id,
                'sku' => 'CAJ-F',
                'preparation_time' => 0,
                'sort_order' => 3,
            ],
        ];

        // Insertar todos los productos
        $allProducts = array_merge(
            $pizzasTradicionales,
            $pizzasEspeciales,
            $pizzasDolce,
            $pizzasMulticereal,
            $calzones,
            $pastichos,
            $ingredientes,
            $ingredientesPremium,
            $cajas
        );
        
        foreach ($allProducts as $productData) {
            Product::create($productData);
        }

        $this->command->info('Productos reales cargados exitosamente:');
        $this->command->info('- ' . count($pizzasTradicionales) . ' Pizzas Tradicionales');
        $this->command->info('- ' . count($pizzasEspeciales) . ' Pizzas Especiales');
        $this->command->info('- ' . count($pizzasDolce) . ' Pizzas Dolce');
        $this->command->info('- ' . count($pizzasMulticereal) . ' Pizzas Multicereal');
        $this->command->info('- ' . count($calzones) . ' Calzones');
        $this->command->info('- ' . count($pastichos) . ' Pastichos');
        $this->command->info('- ' . count($ingredientes) . ' Ingredientes');
        $this->command->info('- ' . count($ingredientesPremium) . ' Ingredientes Premium');
        $this->command->info('- ' . count($cajas) . ' Cajas de Pizza');
        $this->command->info('Total: ' . count($allProducts) . ' productos');
    }
}
