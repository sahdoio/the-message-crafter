<?php

namespace Database\Factories;

use App\Domain\Contact\Entities\Contact;
use App\Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Contact\Entities\Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'phone' => $this->faker->numerify('#############'),
            'email' => $this->faker->email,
            'user_id' => User::factory(),
        ];
    }
}
