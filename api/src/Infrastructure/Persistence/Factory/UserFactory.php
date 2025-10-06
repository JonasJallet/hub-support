<?php

namespace App\Infrastructure\Persistence\Factory;

use App\Domain\Entity\User;
use App\Domain\Service\Authentication\AuthenticationInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    public const string PASSWORD = "faVsmX3Tcj#0!?R";

    public function __construct(
        private readonly AuthenticationInterface $authentication,
    )
    {
        parent::__construct();
    }

    protected function defaults(): array
    {
        return [
            'email' => self::faker()->unique()->email(),
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'password' => self::PASSWORD,
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function(User $user) {
                $user->setPassword(
                    $this->authentication->hashPassword($user, self::PASSWORD)
                );
            });
    }

    public static function class(): string
    {
        return User::class;
    }
}
