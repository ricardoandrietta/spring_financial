<?php

use App\Infrastructure\Repositories\UserEloquentRepository;
use App\Models\User as UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create the repository instance
    $this->repository = new UserEloquentRepository();
});

test('resetScores sets all user scores to zero', function () {
    // Arrange: Create users with various scores
    UserModel::factory()->create(['name' => 'User 1', 'score' => 10]);
    UserModel::factory()->create(['name' => 'User 2', 'score' => 50]);
    UserModel::factory()->create(['name' => 'User 3', 'score' => 0]); // Already zero
    UserModel::factory()->create(['name' => 'User 4', 'score' => 75]);

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
    UserModel::factory()->create(['name' => 'High Score', 'score' => 100, 'age' => 25]);
    UserModel::factory()->create(['name' => 'Zero Score', 'score' => 0, 'age' => 30]);
    UserModel::factory()->create(['name' => 'Negative Score', 'score' => -10, 'age' => 35]);

    // Act: Call the repository method
    $this->repository->resetScores();

    // Assert: Only the user with score > 0 should be modified
    assertDatabaseHas('users', ['name' => 'High Score', 'score' => 0, 'age' => 25]);
    assertDatabaseHas('users', ['name' => 'Zero Score', 'score' => 0, 'age' => 30]);
    assertDatabaseHas('users', ['name' => 'Negative Score', 'score' => -10, 'age' => 35]);
});

test('can create a user', function () {
    $user = new \App\Domain\Entities\User(name: 'Test User', age: 25, address: '123 Test St', score: 0);

    $createdUser = $this->repository->create($user);

    expect($createdUser->getId())->not->toBeNull();
    expect($createdUser->getName())->toBe('Test User');
    expect($createdUser->getAge())->toBe(25);
    expect($createdUser->getAddress())->toBe('123 Test St');
    expect($createdUser->getScore())->toBe(0);

    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'age' => 25,
        'address' => '123 Test St',
        'score' => 0,
    ]);
});

test('can find a user by id', function () {
    $userModel = UserModel::create([
        'name' => 'Find Me',
        'age' => 30,
        'address' => '456 Find St',
        'score' => 10,
    ]);

    $foundUser = $this->repository->findById($userModel->id);

    expect($foundUser)->not->toBeNull();
    expect($foundUser->getId())->toBe($userModel->id);
    expect($foundUser->getName())->toBe('Find Me');
});

test('returns null when finding non-existent user', function () {
    $foundUser = $this->repository->findById(999);

    expect($foundUser)->toBeNull();
});

test('can update a user', function () {
    $userModel = UserModel::create([
        'name' => 'Original Name',
        'age' => 35,
        'address' => '789 Original St',
        'score' => 5,
    ]);

    $user = new \App\Domain\Entities\User(
        name: 'Updated Name',
        age: 40,
        score: 15,
        address: '101 Updated St',
        id: $userModel->id
    );

    $updatedUser = $this->repository->update($user);

    expect($updatedUser->getName())->toBe('Updated Name');
    expect($updatedUser->getAge())->toBe(40);
    expect($updatedUser->getAddress())->toBe('101 Updated St');
    expect($updatedUser->getScore())->toBe(15);

    $this->assertDatabaseHas('users', [
        'id' => $userModel->id,
        'name' => 'Updated Name',
        'age' => 40,
        'address' => '101 Updated St',
        'score' => 15,
    ]);
});

//test('can delete a user', function () {
//    $userModel = UserModel::create([
//        'name' => 'To Delete',
//        'age' => 45,
//        'address' => '202 Delete St',
//        'score' => 20,
//    ]);
//
//    $result = $this->repository->delete($userModel->id);
//
//    expect($result)->toBeTrue();
//    $this->assertDatabaseMissing('users', ['id' => $userModel->id]);
//});

test('can add points to a user', function () {
    $userModel = UserModel::create([
        'name' => 'Point User',
        'age' => 50,
        'address' => '303 Point St',
        'score' => 25,
    ]);

    $updatedUser = $this->repository->addPoints($userModel->id, 10);

    expect($updatedUser->getScore())->toBe(35);
    $this->assertDatabaseHas('users', [
        'id' => $userModel->id,
        'score' => 35,
    ]);
});

test('can subtract points from a user', function () {
    $userModel = UserModel::create([
        'name' => 'Subtract User',
        'age' => 55,
        'address' => '404 Subtract St',
        'score' => 40,
    ]);

    $updatedUser = $this->repository->subtractPoints($userModel->id, 15);

    expect($updatedUser->getScore())->toBe(25);
    $this->assertDatabaseHas('users', [
        'id' => $userModel->id,
        'score' => 25,
    ]);
});

test('can get all users ordered by score desc', function () {
    // Create users with different scores
    UserModel::create([
        'name' => 'Low Score',
        'age' => 20,
        'address' => '111 Low St',
        'score' => 5,
    ]);

    UserModel::create([
        'name' => 'High Score',
        'age' => 25,
        'address' => '222 High St',
        'score' => 50,
    ]);

    UserModel::create([
        'name' => 'Mid Score',
        'age' => 30,
        'address' => '333 Mid St',
        'score' => 25,
    ]);

    $users = $this->repository->getAllOrderedByScore();

    expect($users)->toHaveCount(3);
    expect($users[0]->getScore())->toBe(50);
    expect($users[1]->getScore())->toBe(25);
    expect($users[2]->getScore())->toBe(5);
});

test('can get all users ordered by score asc', function () {
    // Create users with different scores
    UserModel::create([
        'name' => 'Low Score',
        'age' => 20,
        'address' => '111 Low St',
        'score' => 5,
    ]);

    UserModel::create([
        'name' => 'High Score',
        'age' => 25,
        'address' => '222 High St',
        'score' => 50,
    ]);

    UserModel::create([
        'name' => 'Mid Score',
        'age' => 30,
        'address' => '333 Mid St',
        'score' => 25,
    ]);

    $users = $this->repository->getAllOrderedByScore('asc');

    expect($users)->toHaveCount(3);
    expect($users[0]->getScore())->toBe(5);
    expect($users[1]->getScore())->toBe(25);
    expect($users[2]->getScore())->toBe(50);
});
