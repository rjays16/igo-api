<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        return [
           // 'client_id' => $this->faker->numberBetween($min = 1, $max = 10),
            'status_id' => 1, //1 - means Active
            'creditor_id' => $this->faker->numberBetween($min = 1, $max = 100),
            'acct_description' => "Loan",
            'acct_number' =>$this->faker->numberBetween($min = 1, $max = 3),
            'debtor_id' => $this->faker->numberBetween($min = 1, $max = 100),
            'term_id' => $this->faker->numberBetween($min = 1, $max = 100),
            'current_rate' => 1.50,
            'note' => $this->faker->text($maxNbChars = 200),
            'origin_date'=>$this->faker->date($format = 'Y-m-d', $max = 'now'),
            'tag' => $this->faker->word,
        ];
    }
}
