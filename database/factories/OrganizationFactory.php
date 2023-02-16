<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'organization' => $this->faker->firstName.' '.$this->faker->lastName." Organization",
            'description' => $this->faker->sentence($nbWords = 6, $variableNbWords = true) ,
        ];
    }
}
