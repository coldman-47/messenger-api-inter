<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserStateProcessor implements ProcessorInterface
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private ProcessorInterface $persistProcessor;

    public function __construct(UserPasswordHasherInterface $passwordHasher, #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')] ProcessorInterface $persistProcessor)
    {
        $this->userPasswordHasher = $passwordHasher;
        $this->persistProcessor = $persistProcessor;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data instanceof User) {
            $data->setPassword($this->userPasswordHasher->hashPassword($data, $data->getPassword()));
        }
        // dd($data);
        $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
