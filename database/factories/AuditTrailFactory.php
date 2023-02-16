<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditTrail>
 */
class AuditTrailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween($min = 1, $max = 10),
            'pages' => $this->faker->randomElement($array = array ('/admin/clients','/admin/accounts','/admin/terms')),
            'activity'=>  $this->faker->sentence($nbWords = 6, $variableNbWords = true),
        ];
    }
}
