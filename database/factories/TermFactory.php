<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Term>
 */
class TermFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $gender=$this->faker->numberBetween($min = 0, $max = 1);
        $firstName=($gender==0)? $this->faker->firstNameMale : $this->faker->firstNameFemale;
        $lastName=$this->faker->lastName;
        return [
            'account_id' =>$this->faker->numberBetween($min = 1, $max = 100),
            'effective_date' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
            'rate' => $this->faker->randomFloat($nbMaxDecimals = 2, $min = 1.5, $max = 50.00),
            'compound_period_id' =>$this->faker->numberBetween($min = 1, $max = 3),
            'note' =>$this->faker->text($maxNbChars = 200),
        ];
    }
}
