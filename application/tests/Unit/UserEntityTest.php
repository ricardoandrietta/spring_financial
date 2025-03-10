<?php

use App\Domain\Entities\User;

test('user entity can be created with required attributes', function () {
    $user = new User('John Doe', 25, 0, '123 Main St');

    expect($user->getName())->toBe('John Doe');
    expect($user->getAge())->toBe(25);
    expect($user->getAddress())->toBe('123 Main St');
    expect($user->getScore())->toBe(0); // Default score
    expect($user->getId())->toBeNull(); // No ID assigned yet
});

test('user entity can be created with all attributes including ID and QR Code Path', function () {
    $user = new User('Jane Smith', 30, 10, '456 Oak Ave', '/storage/qrcodes/qrcode_1.png', 1);

    expect($user->getName())->toBe('Jane Smith');
    expect($user->getAge())->toBe(30);
    expect($user->getAddress())->toBe('456 Oak Ave');
    expect($user->getScore())->toBe(10);
    expect($user->getQrCodePath())->toBe('/storage/qrcodes/qrcode_1.png');
    expect($user->getId())->toBe(1);
});

test('user entity can be converted to array', function () {
    $user = new User(name: 'Bob Dylan', age: 45, score: 15, address: '202 Elm Blvd', id: 2);

    $array = $user->toArray();

    expect($array)->toBe([
        'id' => 2,
        'name' => 'Bob Dylan',
        'age' => 45,
        'score' => 15,
        'address' => '202 Elm Blvd',
        'qrCodePath' => null
    ]);
});

test('user entity can be created from array', function () {
    $data = [
        'id' => 3,
        'name' => 'Charlie Parker',
        'age' => 50,
        'score' => 25,
        'address' => '303 Birch St',
    ];

    $user = User::fromArray($data);

    expect($user->getId())->toBe(3);
    expect($user->getName())->toBe('Charlie Parker');
    expect($user->getAge())->toBe(50);
    expect($user->getScore())->toBe(25);
    expect($user->getAddress())->toBe('303 Birch St');
});

