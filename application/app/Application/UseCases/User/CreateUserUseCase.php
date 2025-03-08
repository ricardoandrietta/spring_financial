<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Application\DTOs\User\CreateUserInputDTO;
use App\Application\DTOs\User\UserOutputDTO;
use App\Domain\Entities\User;
use App\Domain\Repositories\Interfaces\UserRepositoryInterface;

class CreateUserUseCase
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {
    }

    public function execute(CreateUserInputDTO $inputDTO): UserOutputDTO
    {
        $user = new User(
            name: $inputDTO->name,
            age: $inputDTO->age,
            score: 0,
            address: $inputDTO->address
        );

        $createdUser = $this->userRepository->create($user);

        return new UserOutputDTO($createdUser->toArray());
    }
}
