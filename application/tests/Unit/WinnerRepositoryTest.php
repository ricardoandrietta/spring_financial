<?php

use App\Domain\Entities\Winner;
use App\Infrastructure\Repositories\WinnerEloquentRepository;
use App\Models\Winner as WinnerModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Clear database
    WinnerModel::query()->delete();
});

test('it can create a new winner record', function () {
    $userId = 1;
    $score = 100;
    $date = new \DateTime();

    $winner = new Winner($userId, $score, $date);
    $repository = new WinnerEloquentRepository();

    $result = $repository->create($winner);

    // Assert winner was created and ID was set
    expect($result)->toBeInstanceOf(Winner::class);
    expect($result->getId())->not->toBeNull();
    expect($result->getUserId())->toBe($userId);
    expect($result->getScore())->toBe($score);

    // Check database record
    $record = WinnerModel::find($result->getId());
    expect($record)->not->toBeNull();
    expect($record->user_id)->toBe($userId);
    expect($record->score)->toBe($score);
});
