<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Stdlib;

use Jesperbeisner\Fwstats\Enum\FlashEnum;
use Jesperbeisner\Fwstats\Exception\SessionException;
use Jesperbeisner\Fwstats\Interface\SessionInterface;
use Jesperbeisner\Fwstats\Model\User;
use Jesperbeisner\Fwstats\Repository\UserRepository;
use Webmozart\Assert\Assert;

final class Session implements SessionInterface
{
    private bool $started = false;
    private ?User $user = null;

    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function start(): void
    {
        if ($this->started === true) {
            return;
        }

        if (session_start() === false) {
            throw new SessionException('Could not start the session?! o.O');
        }

        $this->started = true;
    }

    public function get(string $key): string|int|float|bool|null
    {
        if ($this->started === false) {
            $this->start();
        }

        return Assert::nullOrScalar($_SESSION[$key] ?? null);
    }

    public function set(string $key, string|int|float|bool $value): void
    {
        if ($this->started === false) {
            $this->start();
        }

        $_SESSION[$key] = $value;
    }

    public function getUser(): ?User
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $username = $this->get('username');

        if ($username === null) {
            return null;
        }

        if (is_string($username) === false) {
            throw new SessionException("Method 'get' did not return a string.");
        }

        $this->user = $this->userRepository->findOneByUsername($username);

        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->set('username', $user->username);
    }

    public function unset(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function setFlash(FlashEnum $flashEnum, string $message): void
    {
        $_SESSION[$flashEnum->value][] = $message;
    }

    public function getFlash(FlashEnum $flashEnum): array
    {
        $messages = $_SESSION[$flashEnum->value] ?? [];

        unset($_SESSION[$flashEnum->value]);

        return $messages;
    }

    public function destroy(): void
    {
        $_SESSION = [];
        $this->user = null;
    }
}
