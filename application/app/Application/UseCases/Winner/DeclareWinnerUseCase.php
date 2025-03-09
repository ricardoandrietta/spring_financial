<?php

declare(strict_types=1);

namespace App\Application\UseCases\Winner;

use App\Domain\Entities\Winner;
use App\Domain\Repositories\Interfaces\UserRepositoryInterface;
use App\Domain\Repositories\Interfaces\WinnerRepositoryInterface;
use DateTime;

class DeclareWinnerUseCase
{

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected WinnerRepositoryInterface $winnerRepository
    ) {
    }

    /**
     * Execute the use case
     *
     * @return ?Winner The new winner or null if there's a tie
     */
    public function execute(): ?Winner
    {
        // Get users with highest score
        $usersWithHighestScore = $this->userRepository->findUsersWithHighestScore();

        // If there's a tie (more than one user with the highest score), no winner is declared
        if (count($usersWithHighestScore) != 1) {
            return null;
        }

        // Create and save a new winner
        $user = $usersWithHighestScore[0];
        $winner = new Winner(
            userId: $user->getId(),
            score: $user->getScore(),
            createdAt: new DateTime()
        );

        return $this->winnerRepository->create($winner);
    }
}
