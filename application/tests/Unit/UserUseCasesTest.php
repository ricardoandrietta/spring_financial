<?php

use App\Application\DTOs\User\CreateUserInputDTO;
use App\Application\DTOs\User\UpdateScoreInputDTO;
use App\Application\UseCases\User\CreateUserUseCase;
use App\Application\UseCases\User\UpdateScoreUseCase;
use App\Application\Exceptions\UserNotFoundException;
use App\Domain\Entities\User;
use App\Domain\Enums\ScoreOperationEnum;
use App\Domain\Repositories\Interfaces\UserRepositoryInterface;

/*
|--------------------------------------------------------------------------
| CreateUserUseCase
|--------------------------------------------------------------------------
*/
test('create user use case creates a user', function () {
    // Prepare user to be returned by repository
    $user = new User(name: 'Test User', age: 25, score: 0, address: '123 Test St', id: 1);

    // Create mock repository
    $repository = Mockery::mock(UserRepositoryInterface::class);
    $repository->shouldReceive('create')
        ->once()
        ->andReturn($user);

    // Create input DTO with required data
    $inputDTO = new CreateUserInputDTO('Test User', 25, '123 Test St');

    // Execute the use case
    $useCase = new CreateUserUseCase($repository);
    $outputDTO = $useCase->execute($inputDTO);

    // Verify output
    expect($outputDTO->user)->toBe($user->toArray());
});

/*
|--------------------------------------------------------------------------
| UpdateScoreUseCase
|--------------------------------------------------------------------------
*/
test('update score use case adds points to user', function () {
    // Prepare user to be returned by repository
    $user = new User(name: 'Test User', age: 25, score: 10, address: '123 Test St', id: 1);

    // Create mock repository
    $repository = Mockery::mock(UserRepositoryInterface::class);
    $repository->shouldReceive('addPoints')
        ->with(1, 1)
        ->once()
        ->andReturn($user);

    // Create input DTO with required data
    $inputDTO = new UpdateScoreInputDTO(1, ScoreOperationEnum::Add);

    // Execute the use case
    $useCase = new UpdateScoreUseCase($repository);
    $outputDTO = $useCase->execute($inputDTO);

    // Verify output
    expect($outputDTO->user)->toBe($user->toArray());
});

test('update score use case subtracts points from user', function () {
    // Prepare user to be returned by repository
    $user = new User(name: 'Test User', age: 25, score: 5, address: '123 Test St', id: 1);

    // Create mock repository
    $repository = Mockery::mock(UserRepositoryInterface::class);
    $repository->shouldReceive('subtractPoints')
        ->with(1, 1)
        ->once()
        ->andReturn($user);

    // Create input DTO with required data
    $inputDTO = new UpdateScoreInputDTO(1, ScoreOperationEnum::Subtract);

    // Execute the use case
    $useCase = new UpdateScoreUseCase($repository);
    $outputDTO = $useCase->execute($inputDTO);

    // Verify output
    expect($outputDTO->user)->toBe($user->toArray());
});

test('update score use case throws exception when user not found', function () {
    // Create mock repository
    $repository = Mockery::mock(UserRepositoryInterface::class);
    $repository->shouldReceive('addPoints')
        ->with(999, 1)
        ->once()
        ->andReturn(null);

    // Create input DTO with non-existent user ID
    $inputDTO = new UpdateScoreInputDTO(999, ScoreOperationEnum::Add);

    // Execute the use case and expect exception
    $useCase = new UpdateScoreUseCase($repository);

    expect(fn() => $useCase->execute($inputDTO))
        ->toThrow(UserNotFoundException::class, 'User not found with ID: 999');
});
