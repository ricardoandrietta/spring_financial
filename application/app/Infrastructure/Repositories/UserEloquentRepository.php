<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Models\User;

class UserEloquentRepository implements \App\Domain\Repositories\Interfaces\UserRepositoryInterface
{

    public function resetScores(): void
    {
        User::where('score', '>', 0)->update(['score' => 0]);
    }
}
