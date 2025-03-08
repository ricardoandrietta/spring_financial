<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Domain\Repositories\Interfaces\UserRepositoryInterface;

class DeleteUserUseCase
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {
    }

    public function execute(int $userId): void
    {
        $this->userRepository->delete($userId);
    }
}
