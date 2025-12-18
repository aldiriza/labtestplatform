<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_description' => $this->faker->randomElement([
                'Polyurethane Sheet 2mm', 'Steel Bolt M6', 'Aluminum Alloy Plate', 'Resin Adhesive X200',
                'Cotton Thread #40', 'Polyester Fabric 600D', 'Latex Foam Padding', 'Rubber Gasket Seal',
                'Copper Wire 0.5mm', 'Plastic Buckle 25mm'
            ]),
            'part_number' => strtoupper($this->faker->bothify('##-###-####')),
            'specification' => $this->faker->randomElement(['ISO 9001 Grade', 'Type A', 'High Tensile', 'Water Resistant', 'Heat Treated']),
            'brand' => $this->faker->company(),
            'category' => $this->faker->randomElement(['Raw Material', 'Consumable', 'Spare Part', 'Packaging']),
            'unit' => $this->faker->randomElement(['PCS', 'KG', 'MTR', 'ROL', 'BOX', 'LOT']),
            'location' => $this->faker->randomElement(['Warehouse A-1', 'Warehouse B-2', 'Shelf C-3', 'Rack D-4']),
            'minimum_stock' => $this->faker->numberBetween(10, 100),
            
            'supplier' => $this->faker->company(),
            'po_number' => strtoupper($this->faker->bothify('PO-#####')),
            'lot_number' => strtoupper($this->faker->bothify('LOT-###')),
            'quantity' => $this->faker->numberBetween(50, 5000),
            
            'status' => $this->faker->randomElement([
                'scheduled', 
                'arrived', 
                'lab_ready_for_pickup', 
                'lab_in_progress', 
                'completed', 
                'rejected'
            ]),
            'lot_arrival_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
