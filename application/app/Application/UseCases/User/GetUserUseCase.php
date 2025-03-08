<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Application\DTOs\User\UserOutputDTO;
use App\Domain\Repositories\Interfaces\UserRepositoryInterface;
use UserNotFoundException;

class GetUserUseCase
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {
    }

    /**
     * @param int $userId
     * @return UserOutputDTO
     * @throws UserNotFoundException
     */
    public function execute(int $userId): UserOutputDTO
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new UserNotFoundException("User not found with ID: {$userId}");
        }

        return new UserOutputDTO($user->toArray());
    }
}
