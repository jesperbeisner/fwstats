<?php

declare(strict_types=1);

namespace Fwstats\Models;

use BadMethodCallException;
use DateTime;
use Fwstats\Enums\RoleEnum;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $email
 * @property string $password
 * @property RoleEnum $role
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
final class User extends Model implements Authenticatable
{
    use HasFactory;

    protected $casts = [
        'role' => RoleEnum::class,
    ];

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): int
    {
        return $this->id;
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }

    public function getRememberToken(): string
    {
        throw new BadMethodCallException(sprintf('Method "%s" is not implemented', __METHOD__));
    }

    public function setRememberToken(mixed $value): void
    {
        throw new BadMethodCallException(sprintf('Method "%s" is not implemented', __METHOD__));
    }

    public function getRememberTokenName(): string
    {
        throw new BadMethodCallException(sprintf('Method "%s" is not implemented', __METHOD__));
    }
}
