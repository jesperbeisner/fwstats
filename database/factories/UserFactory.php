<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

/**
 * @extends Factory<User>
 */
final class UserFactory extends Factory
{
    public const string DEFAULT_PASSWORD = 'Password12345?';

    public const string DEFAULT_PASSWORD_HASH = '$2y$12$LjJOSvRIROePrQZC5Sf6luTuF6jogskuaUYeL9IKsoc0XBup1SF22';

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => Uuid::uuid4()->toString(),
            'username' => $this->faker->unique()->userName(),
            'password' => self::DEFAULT_PASSWORD_HASH,
            'remember_token' => Str::random(50),
        ];
    }

    public function withUsername(string $username): self
    {
        return $this->set('username', $username);
    }
}
