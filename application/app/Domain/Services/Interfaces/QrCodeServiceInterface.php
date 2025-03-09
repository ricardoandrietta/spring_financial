<?php

namespace App\Domain\Services\Interfaces;
use App\Domain\Entities\User;
use App\Domain\Repositories\Interfaces\UserRepositoryInterface;

interface QrCodeServiceInterface
{
    /**
     * Generate a QR code for the user's address.
     *
     * @param User $user
     * @param UserRepositoryInterface $userRepository
     * @return void
     */
    public function generateQrCodeForUserAddress(User $user, UserRepositoryInterface $userRepository);
}
