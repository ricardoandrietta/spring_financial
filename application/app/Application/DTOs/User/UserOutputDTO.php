<?php

declare(strict_types=1);

namespace App\Application\DTOs\User;

class UserOutputDTO
{
    public function __construct(public array $user)
    {
    }
}
