<?php

use App\Domain\Entities\User;
use App\Domain\Entities\Winner;
use App\Domain\Repositories\Interfaces\UserRepositoryInterface;
use App\Domain\Repositories\Interfaces\WinnerRepositoryInterface;
use App\Infrastructure\Jobs\DeclareWinnerJob;
use App\Models\User as UserModel;
use App\Models\Winner as WinnerModel;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;

test('winners are correctly determined and recorded', function () {
    // Create test users
    $highScoreUser = UserModel::create(['name' => 'Highest Score User', 'score' => 100, 'age' => 18, 'address' => 'Test Addr 1']);
    UserModel::create(['name' => 'Medium Score User', 'score' => 50, 'age' => 18, 'address' => 'Test Addr 2']);
    UserModel::create(['name' => 'Low Score User', 'score' => 25, 'age' => 18, 'address' => 'Test Addr 3']);

    // Create mocks
    $userRepository = Mockery::mock(UserRepositoryInterface::class);
    $winnerRepository = Mockery::mock(WinnerRepositoryInterface::class);

    // Configure user repository to return the user with highest score
    $highestScoreUser = new User(
        name: 'Highest Score User',
        age: 18,
        score: 100,
        address: 'Test Addr 1',
        id: $highScoreUser->id
    );
    $userRepository->shouldReceive('findUsersWithHighestScore')
        ->once()
        ->andReturn([$highestScoreUser]);

    $winner = new Winner(
        userId: $highScoreUser->id,
        score: 100
    );

    // Configure winner repository to accept any Winner instance
    $winnerRepository->shouldReceive('create')
        ->once()
        ->with(Mockery::type(Winner::class))
        ->andReturnUsing(function (Winner $input) use ($winner, $highScoreUser) {
            // Simulate creating a winner record
            $winnerModel = WinnerModel::create([
                'user_id' => $input->getUserId(),
                'score' => $input->getScore(),
                'created_at' => $input->getCreatedAt(),
            ]);

            // Return the winner with an ID
            return $winner;
        });

    // Create and execute the job
    $job = new DeclareWinnerJob($userRepository, $winnerRepository);
    $job->handle();

    // Check that a winner record was created
    $winners = WinnerModel::all();
    expect($winners)->toHaveCount(1);
    expect($winners[0]->user_id)->toBe($highScoreUser->id);
    expect($winners[0]->score)->toBe(100);
});

test('no winner is recorded when there is a tie', function () {
    // Create test users with tied scores
    UserModel::create(['name' => 'First Tied User', 'score' => 100, 'age' => 18, 'address' => 'Test Addr 1']);
    UserModel::create(['name' => 'Second Tied User', 'score' => 100, 'age' => 20, 'address' => 'Test Addr 2']);
    UserModel::create(['name' => 'Low Score User', 'score' => 25, 'age' => 22, 'address' => 'Test Addr 3']);

    // Create mocks
    $userRepository = Mockery::mock(UserRepositoryInterface::class);
    $winnerRepository = Mockery::mock(WinnerRepositoryInterface::class);

    // Configure user repository to return multiple users with highest score (tie)
    $tiedUser1 = new User(name: 'First Tied User', age: 18, score: 100, address: 'Test Addr 1', id: 1);
    $tiedUser2 = new User(name: 'Second Tied User', age: 20, score: 100, address: 'Test Addr 2', id: 2);

    $userRepository->shouldReceive('findUsersWithHighestScore')
        ->once()
        ->andReturn([$tiedUser1, $tiedUser2]);

    // Winner repository should not be called at all in a tie
    $winnerRepository->shouldReceive('create')->never();

    // Create and execute the job
    $job = new DeclareWinnerJob($userRepository, $winnerRepository);
    $job->handle();

    // Check that no winner record was created
    $winners = WinnerModel::all();
    expect($winners)->toHaveCount(0);
});

test('schedule runs job every five minutes', function () {
    // Get the scheduler instance
    $schedule = app(Schedule::class);

    // Get all the scheduled events
    $events = $schedule->events();

    // Filter to find events that run DeclareWinnerJob
    $winnerJobEvents = array_filter($events, function ($event) {
        // For Laravel 12, we need to inspect the event description
        // The description format may vary depending on Laravel's implementation
        $description = $event->description ?? '';
        return strpos($description, 'DeclareWinnerJob') !== false;
    });

    // Check that we found at least one matching event
    expect(count($winnerJobEvents))->toBeGreaterThan(0);

    // Check that it's scheduled every five minutes
    $event = reset($winnerJobEvents);
    expect($event->expression)->toBe('*/5 * * * *');
});
