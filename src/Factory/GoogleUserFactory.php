<?php

namespace App\Factory;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class GoogleUserFactory
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function create(array $parameters): UserInterface
    {
        $user = new User();
        $user
            ->setEmail($parameters['email'])
            ->setToken($parameters['token'])
            ->setRoles(['ROLE_USER']);
        $this->userRepository->add($user, true);
        return $user;
    }
}