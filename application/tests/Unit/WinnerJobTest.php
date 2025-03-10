<?php

use App\Domain\Entities\User;
use App\Domain\Entities\Winner;
use App\Domain\Repositories\Interfaces\UserRepositoryInterface;
use App\Domain\Repositories\Interfaces\WinnerRepositoryInterface;
use App\Infrastructure\Jobs\DeclareWinnerJob;
use Illuminate\Support\Facades\Log;

test('job logs winner information when winner is found', function () {
    // Create mock repositories
    $userRepository = Mockery::mock(UserRepositoryInterface::class);
    $winnerRepository = Mockery::mock(WinnerRepositoryInterface::class);

    // Create a test user with high score
    $highScoreUser = new User(
        name: 'Highest Score User',
        age: 18,
        score: 100,
        address: 'Test Addr 1',
        id: 1
    );

    // Mock userRepository to return the high score user
    $userRepository->shouldReceive('findUsersWithHighestScore')
        ->once()
        ->andReturn([$highScoreUser]);

    // Create a winner that will be returned
    $winner = new Winner(
        userId: 1,
        score: 100
    );

    // Mock winnerRepository to return the created winner
    $winnerRepository->shouldReceive('create')
        ->once()
        ->with(Mockery::type(Winner::class))
        ->andReturn($winner);

    // Mock Log facade
    Log::shouldReceive('debug')->with('DeclareWinnerJob:construct')->once();
    Log::shouldReceive('debug')->with('DeclareWinnerJob:handle')->once();
    Log::shouldReceive('info')
        ->with('New winner declared', Mockery::on(function ($context) {
            return isset($context['user_id']) &&
                $context['user_id'] === 1 &&
                isset($context['score']) &&
                $context['score'] === 100 &&
                isset($context['time']);
        }))
        ->once();

    // Create instance of job with the repositories
    $job = new DeclareWinnerJob($userRepository, $winnerRepository);

    // Execute job
    $job->handle();
});

test('job logs tie information when no winner is found', function () {
    // Create mock repositories
    $userRepository = Mockery::mock(UserRepositoryInterface::class);
    $winnerRepository = Mockery::mock(WinnerRepositoryInterface::class);

    // Create tied users
    $tiedUser1 = new User(name: 'First Tied User', age: 18, score: 100, address: 'Test Addr 1', id: 1);
    $tiedUser2 = new User(name: 'Second Tied User', age: 20, score: 100, address: 'Test Addr 2', id: 2);

    // Mock userRepository to return multiple users (indicating a tie)
    $userRepository->shouldReceive('findUsersWithHighestScore')
        ->once()
        ->andReturn([$tiedUser1, $tiedUser2]);

    // Winner repository should not be called in a tie
    $winnerRepository->shouldReceive('create')->never();

    // Mock Log facade
    Log::shouldReceive('debug')->with('DeclareWinnerJob:construct')->once();
    Log::shouldReceive('debug')->with('DeclareWinnerJob:handle')->once();

    // More flexible with the info message - use withArgs instead of with
    Log::shouldReceive('info')
        ->withArgs(function ($message, $context = null) {
            return str_contains($message, 'No winner declared due to a tie');
        })
        ->once();

    // Create instance of job with the repositories
    $job = new DeclareWinnerJob($userRepository, $winnerRepository);

    // Execute job
    $job->handle();
});

test('job logs when no users are found', function () {
    // Create mock repositories
    $userRepository = Mockery::mock(UserRepositoryInterface::class);
    $winnerRepository = Mockery::mock(WinnerRepositoryInterface::class);

    // Mock userRepository to return empty array (no users)
    $userRepository->shouldReceive('findUsersWithHighestScore')
        ->once()
        ->andReturn([]);

    // Winner repository should not be called when no users
    $winnerRepository->shouldReceive('create')->never();

    // Mock Log facade
    Log::shouldReceive('debug')->with('DeclareWinnerJob:construct')->once();
    Log::shouldReceive('debug')->with('DeclareWinnerJob:handle')->once();

    // Use the same message as in the tie case
    Log::shouldReceive('info')
        ->withArgs(function ($message, $context = null) {
            return str_contains($message, 'No winner declared due to a tie');
        })
        ->once();

    // Create instance of job with the repositories
    $job = new DeclareWinnerJob($userRepository, $winnerRepository);

    // Execute job
    $job->handle();
});
