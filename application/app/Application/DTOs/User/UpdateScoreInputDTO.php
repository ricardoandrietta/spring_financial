<?php

declare(strict_types=1);

namespace App\Application\DTOs\User;

use App\Domain\Enums\ScoreOperationEnum;

class UpdateScoreInputDTO
{
    public int $points = 1;

    public function __construct(public int $userId, public ScoreOperationEnum $operation)
    {
    }
}
