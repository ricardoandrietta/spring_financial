<?php

declare(strict_types=1);

namespace App\Application\DTOs\User;

class CreateUserInputDTO
{
    public function __construct(public string $name, public int $age, public string $address)
    {
    }
}
