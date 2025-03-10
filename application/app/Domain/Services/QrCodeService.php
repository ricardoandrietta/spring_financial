<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Entities\User;
use App\Domain\Repositories\Interfaces\UserRepositoryInterface;
use App\Infrastructure\Adapters\GoQrAdapter;
use App\Infrastructure\Jobs\GenerateUserQrCodeJob;

class QrCodeService implements Interfaces\QrCodeServiceInterface
{

    /**
     * @inheritDoc
     */
    public function generateQrCodeForUserAddress(User $user, UserRepositoryInterface $userRepository)
    {
        GenerateUserQrCodeJob::dispatch($user, $userRepository, new GoQrAdapter());
    }
}
