<?php

declare(strict_types=1);

namespace App\Application\DTOs\User;

class GetAllUsersOutputDTO
{
    public array $users;

    public function __construct(array $users)
    {
        $this->users = $users;
    }
}
