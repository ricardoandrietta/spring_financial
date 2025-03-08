<?php

use App\Infrastructure\Repositories\UserEloquentRepository;
use App\Models\User;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    // Create the repository instance
    $this->repository = new UserEloquentRepository();
});

test('resetScores sets all user scores to zero', function () {
    // Arrange: Create users with various scores
    User::factory()->create(['name' => 'User 1', 'score' => 10]);
    User::factory()->create(['name' => 'User 2', 'score' => 50]);
    User::factory()->create(['name' => 'User 3', 'score' => 0]); // Already zero
    User::factory()->create(['name' => 'User 4', 'score' => 75]);

    // Act: Call the repository method
    $this->repository->resetScores();

    // Assert: All users with scores > 0 should now have score = 0
    assertDatabaseHas('users', ['name' => 'User 1', 'score' => 0]);
    assertDatabaseHas('users', ['name' => 'User 2', 'score' => 0]);
    assertDatabaseHas('users', ['name' => 'User 3', 'score' => 0]);
    assertDatabaseHas('users', ['name' => 'User 4', 'score' => 0]);

    // Ensure no users have scores greater than 0
    assertDatabaseMissing('users', ['score' => '>', 0]);
});

test('resetScores only affects users with scores greater than zero', function () {
    // Arrange: Create users with various properties
    User::factory()->create(['name' => 'High Score', 'score' => 100, 'age' => 25]);
    User::factory()->create(['name' => 'Zero Score', 'score' => 0, 'age' => 30]);
    User::factory()->create(['name' => 'Negative Score', 'score' => -10, 'age' => 35]);

    // Act: Call the repository method
    $this->repository->resetScores();

    // Assert: Only the user with score > 0 should be modified
    assertDatabaseHas('users', ['name' => 'High Score', 'score' => 0, 'age' => 25]);
    assertDatabaseHas('users', ['name' => 'Zero Score', 'score' => 0, 'age' => 30]);
    assertDatabaseHas('users', ['name' => 'Negative Score', 'score' => -10, 'age' => 35]);
});
