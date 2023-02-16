<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
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
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $firstName.' '.$lastName,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),

            'address1' => $this->faker->streetAddress,
            'address2' => $this->faker->secondaryAddress,
            'city_id' => $this->faker->numberBetween($min = 1, $max = 10),
            'state' => $this->faker->randomElement($array = array ('MA','CA','NY','KY','TX','AL')),
            'zip' => substr($this->faker->postcode,0,5),
            'phone' => $this->faker->e164PhoneNumber,
            'role_id' => $this->faker->numberBetween($min = 1, $max = 4),
            'client_id' =>0,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
