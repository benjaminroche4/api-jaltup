<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Hashes the password of a User entity before persisting it.
 */
#[AsDecorator('api_platform.doctrine.orm.state.persist_processor')]
readonly class UserHashPasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface          $innerProcessor,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if($data instanceof User && $data->getPlainPassword() !== null) {
            $data->setPassword($this->userPasswordHasher->hashPassword($data, $data->getPlainPassword()));
            $data->eraseCredentials();
        }

        return $this->innerProcessor->process($data, $operation, $uriVariables, $context);
    }
}
