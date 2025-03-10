<?php

declare(strict_types=1);

namespace App\Application\UseCases\User;

use App\Application\DTOs\User\UserOutputDTO;
use App\Application\Exceptions\UserNotFoundException;
use App\Domain\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Storage;


class GetUserUseCase
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {
    }

    /**
     * @param int $userId
     * @return UserOutputDTO
     * @throws UserNotFoundException
     */
    public function execute(int $userId): UserOutputDTO
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new UserNotFoundException("User not found with ID: {$userId}");
        }

        $output = $user->toArray();
        $output['qrBase64Img'] = null;
        if (!is_null($user->getQrCodePath()) && Storage::exists($user->getQrCodePath())) {
            $output['qrBase64Img'] = base64_encode(Storage::get($user->getQrCodePath()));
        }

        return new UserOutputDTO($output);
    }
}
