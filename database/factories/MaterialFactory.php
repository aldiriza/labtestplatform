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
        $status = $this->faker->randomElement(\App\Enums\MaterialStatus::cases());

        $dateIncoming = null;
        $labReceivedAt = null;
        $testingStartedAt = null;
        $testCompletedAt = null;

        // Logic to populate timestamps based on status
        if ($status !== \App\Enums\MaterialStatus::Scheduled) {
            $dateIncoming = $this->faker->dateTimeBetween('-1 month', 'now');
        }

        if (in_array($status, [\App\Enums\MaterialStatus::LabReceived, \App\Enums\MaterialStatus::InProgress, \App\Enums\MaterialStatus::Completed, \App\Enums\MaterialStatus::Rejected])) {
            $labReceivedAt = $this->faker->dateTimeBetween($dateIncoming, 'now');
        }

        if (in_array($status, [\App\Enums\MaterialStatus::InProgress, \App\Enums\MaterialStatus::Completed, \App\Enums\MaterialStatus::Rejected])) {
            $testingStartedAt = $this->faker->dateTimeBetween($labReceivedAt, 'now');
        }

        if (in_array($status, [\App\Enums\MaterialStatus::Completed, \App\Enums\MaterialStatus::Rejected])) {
            $testCompletedAt = $this->faker->dateTimeBetween($testingStartedAt, 'now');
        }

        return [
            // Master Data
            'lab_po_number' => 'LAB-' . strtoupper($this->faker->bothify('PO-####')),
            'po_number' => strtoupper($this->faker->bothify('PO-#####')),
            'lot_number' => strtoupper($this->faker->bothify('LOT-###')),
            'supplier' => $this->faker->company(),
            'country_of_supplier' => $this->faker->country(),
            'material_group' => $this->faker->word(),
            'material_type' => $this->faker->word(),
            'material_name' => $this->faker->words(3, true),
            'color' => $this->faker->colorName(),
            'color_key' => strtoupper($this->faker->bothify('??#')),
            'mpn' => strtoupper($this->faker->bothify('MPN-####')),
            'article_style' => strtoupper($this->faker->bothify('ART-####')),
            'component' => $this->faker->word(),
            'qty' => $this->faker->numberBetween(10, 1000),
            'bm' => $this->faker->word(),

            // Status & Timestamps
            'status' => $status,
            'date_incoming' => $dateIncoming,
            'time_incoming' => $dateIncoming ? $dateIncoming->format('H:i:s') : null,
            'lab_received_at' => $labReceivedAt,
            'testing_started_at' => $testingStartedAt,
            'test_completed_at' => $testCompletedAt,
        ];
    }
}
