<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories.Factory<\App\Models\Warz>
 */
class WarzFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'topic' => fake()->sentence(),
            'warrior_names' => json_encode([fake()->name(), fake()->name()]),
            'warrior_contacts' => json_encode([fake()->email(), fake()->email()]),
            'prize' => fake()->randomElement(['$100', '$500', '$1000']),
            'user_id' => 1,
        ];
    }
}
