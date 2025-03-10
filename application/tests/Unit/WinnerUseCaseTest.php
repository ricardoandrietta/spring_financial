<?php

use App\Application\UseCases\Winner\DeclareWinnerUseCase;
use App\Domain\Entities\User;
use App\Domain\Entities\Winner;
use App\Domain\Repositories\Interfaces\UserRepositoryInterface;
use App\Domain\Repositories\Interfaces\WinnerRepositoryInterface;

test('it declares a winner when there is a single user with highest score', function () {
    // Create mock repositories
    $userRepository = Mockery::mock(UserRepositoryInterface::class);
    $winnerRepository = Mockery::mock(WinnerRepositoryInterface::class);

    // Configure user repository to return one user with highest score
    $highestScoreUser = new User(name: 'John Doe', age: 18, score: 100, address: 'Test Address', id: 1);
    $userRepository->shouldReceive('findUsersWithHighestScore')
        ->once()
        ->andReturn([$highestScoreUser]);

    // Configure winner repository to return a winner when created
    $winner = new Winner(1, 100);
    $winnerWithId = (new Winner(1, 100))->setId(1);
    $winnerRepository->shouldReceive('create')
        ->once()
        ->with(Mockery::on(function ($arg) use ($winner) {
            return $arg->getUserId() === $winner->getUserId()
                && $arg->getScore() === $winner->getScore();
        }))
        ->andReturn($winnerWithId);

    // Create use case with mock repositories
    $useCase = new DeclareWinnerUseCase($userRepository, $winnerRepository);

    // Execute use case
    $result = $useCase->execute();

    // Assert result is a winner with expected properties
    expect($result)->toBeInstanceOf(Winner::class);
    expect($result->getUserId())->toBe(1);
    expect($result->getScore())->toBe(100);
});

test('it returns null when there is a tie for highest score', function () {
    // Create mock repositories
    $userRepository = Mockery::mock(UserRepositoryInterface::class);
    $winnerRepository = Mockery::mock(WinnerRepositoryInterface::class);

    // Configure user repository to return multiple users with highest score (tie)
    $user1 = new User(name: 'John Doe', age: 18, score: 100, address: 'Test Address Def');
    $user2 = new User(name: 'Jane Doe', age: 20, score: 100, address: 'Test Address Abc');
    $userRepository->shouldReceive('findUsersWithHighestScore')
        ->once()
        ->andReturn([$user1, $user2]);

    // Winner repository should not be called
    $winnerRepository->shouldReceive('create')->never();

    // Create use case with mock repositories
    $useCase = new DeclareWinnerUseCase($userRepository, $winnerRepository);

    // Execute use case
    $result = $useCase->execute();

    // Assert result is null
    expect($result)->toBeNull();
});

test('it returns null when there are no users', function () {
    // Create mock repositories
    $userRepository = Mockery::mock(UserRepositoryInterface::class);
    $winnerRepository = Mockery::mock(WinnerRepositoryInterface::class);

    // Configure user repository to return empty array
    $userRepository->shouldReceive('findUsersWithHighestScore')
        ->once()
        ->andReturn([]);

    // Winner repository should not be called
    $winnerRepository->shouldReceive('create')->never();

    // Create use case with mock repositories
    $useCase = new DeclareWinnerUseCase($userRepository, $winnerRepository);

    // Execute use case
    $result = $useCase->execute();

    // Assert result is null
    expect($result)->toBeNull();
});
