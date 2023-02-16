<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
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
            'account_id' => $this->faker->numberBetween($min = 1, $max = 10),
            'effective_date' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
            'trans_type_id' => $this->faker->numberBetween($min = 1, $max = 5),
            'memo' => $this->faker->sentence($nbWords = 6, $variableNbWords = true),
            'amount' =>$this->faker->randomFloat($nbMaxDecimals = 2, $min = 1.5, $max = 99.99),
            'entry_date' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
        ];
    }
}
