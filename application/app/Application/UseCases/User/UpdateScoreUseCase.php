<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Application\DTOs\User\UpdateScoreInputDTO;
use App\Application\DTOs\User\UserOutputDTO;
use App\Application\Exceptions\UserNotFoundException;
use App\Domain\Enums\ScoreOperationEnum;
use App\Domain\Repositories\Interfaces\UserRepositoryInterface;

class UpdateScoreUseCase
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {
    }

    /**
     * @param UpdateScoreInputDTO $inputDTO
     * @return UserOutputDTO
     * @throws UserNotFoundException
     */
    public function execute(UpdateScoreInputDTO $inputDTO): UserOutputDTO
    {
        if ($inputDTO->operation === ScoreOperationEnum::Add) {
            $user = $this->userRepository->addPoints($inputDTO->userId, $inputDTO->points);
        } else {
            $user = $this->userRepository->subtractPoints($inputDTO->userId, $inputDTO->points);
        }

        if (!$user) {
            throw new UserNotFoundException("User not found with ID: {$inputDTO->userId}");
        }

        return new UserOutputDTO($user->toArray());
    }
}
