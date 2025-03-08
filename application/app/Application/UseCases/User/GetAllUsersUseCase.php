<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Application\DTOs\User\GetAllUsersOutputDTO;
use App\Domain\Repositories\Interfaces\UserRepositoryInterface;

class GetAllUsersUseCase
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(): GetAllUsersOutputDTO
    {
        $users = $this->userRepository->getAllOrderedByScore();

        $usersArray = array_map(function ($user) {
            return $user->toArray();
        }, $users);

        return new GetAllUsersOutputDTO($usersArray);
    }
}
