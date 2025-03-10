<?php

use App\Domain\Entities\Winner;

test('it can create a winner entity', function () {
    $userId = 1;
    $score = 100;

    $winner = new Winner($userId, $score);

    expect($winner->getUserId())->toBe($userId);
    expect($winner->getScore())->toBe($score);
    expect($winner->getCreatedAt())->toBeInstanceOf(\DateTime::class);
});

test('it can set and get winner id', function () {
    $userId = 1;
    $score = 100;
    $id = 5;

    $winner = new Winner($userId, $score);
    $winner->setId($id);

    expect($winner->getId())->toBe($id);
});

test('it can create a winner with a specific date', function () {
    $userId = 1;
    $score = 100;
    $date = new \DateTime('2025-01-01');

    $winner = new Winner($userId, $score, $date);

    expect($winner->getCreatedAt())->toBe($date);
});

test('id is null by default', function () {
    $winner = new Winner(1, 100);

    expect($winner->getId())->toBeNull();
});
