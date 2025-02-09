<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
final class UserFactory extends Factory
{
    private const string PASSWORD_HASH = '$2y$12$jULrA89NHZfpEhU6DdQHM.0d1Tn5SRAUjJKQc.dgM8SZG8Nzk9gly'; // Password12345

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'email' => fake()->unique()->safeEmail(),
            'password' => self::PASSWORD_HASH,
            'remember_token' => Str::random(10),
        ];
    }
}
