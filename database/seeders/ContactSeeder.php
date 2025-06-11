<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Contact\Entities\Contact;
use App\Domain\User\Entities\User;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        Contact::factory()->create([
            'name' => 'Lucas Sahdo',
            'phone' => '5511970954944',
            'email' => 'lucassahdo@gmail.com'
        ]);

        // Create 10 users with 3 contacts each
        User::factory()
            ->count(10)
            ->create()
            ->each(function ($user) {
                Contact::factory()->count(3)->create([
                    'user_id' => $user->id,
                ]);
            });
    }
}
