<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
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
        $randomNumber=$this->faker->numberBetween($min = 1, $max = 999999);
        $email= $firstName.$randomNumber.".".$lastName."@".$this->faker->freeEmailDomain;

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'gender'=>($gender==0)? "Male" : "Female",
            'date_of_birth'=>$this->faker->date($format = 'Y-m-d', $max = 'now'),
            'email' => $email,
            'phone' => $this->faker->e164PhoneNumber,
            'organization_id' => $this->faker->numberBetween($min = 1, $max = 100),
            'address1' => $this->faker->streetAddress,
            'address2' => $this->faker->secondaryAddress,
            'city_id' => $this->faker->numberBetween($min = 1, $max = 10),
            'state' => $this->faker->randomElement($array = array ('MA','CA','NY','KY','TX','AL')),
            'zip' => substr($this->faker->postcode,0,5),
            'client_type_id' => $this->faker->numberBetween($min = 1, $max = 6),
            'ca_date' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
            'tag' => $this->faker->word,
            'note' => $this->faker->text($maxNbChars = 200),
        ];
    }
}
