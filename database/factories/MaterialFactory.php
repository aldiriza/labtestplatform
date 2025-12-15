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
            'material_name' => $this->faker->randomElement(['Synthetic Leather X1', 'Mesh Fabric Pro', 'Rubber Sole Grade A', 'Canvas 10oz', 'Nylon 600D', 'Suede Premium']),
            'supplier' => $this->faker->company(),
            'color' => $this->faker->colorName(),
            'shoe_style' => $this->faker->randomElement(['Running V1', 'Hiking Boot', 'Casual Sneaker', 'Performance Elite']),
            'article_no' => strtoupper($this->faker->bothify('ART-####')),
            'po_number' => strtoupper($this->faker->bothify('PO-#####')),
            'lot_number' => strtoupper($this->faker->bothify('LOT-###')),
            'quantity' => $this->faker->numberBetween(50, 5000),
            'status' => 'scheduled',
            'lot_arrival_date' => null,
        ];
    }
}
