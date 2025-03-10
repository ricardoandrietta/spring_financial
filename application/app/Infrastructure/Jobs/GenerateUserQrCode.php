<?php

declare(strict_types=1);

namespace App\Infrastructure\Jobs;

use App\Domain\Entities\User;
use App\Domain\Jobs\Interfaces\QrCodeGenerationJobInterface;
use App\Domain\Repositories\Interfaces\UserRepositoryInterface;
use App\Domain\Services\Interfaces\QrCodeAdapterInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GenerateUserQrCode implements QrCodeGenerationJobInterface, ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param UserRepositoryInterface $userRepository
     * @param QrCodeAdapterInterface $qrCodeGenerator
     */
    public function __construct(
        protected User $user,
        protected UserRepositoryInterface $userRepository,
        protected QrCodeAdapterInterface $qrCodeGenerator,
    ) {
    }

    public function getUserId(): int
    {
        return $this->user->getId();
    }

    /**
     * @inheritDoc
     */
    public function handle(): void
    {
        try {
            // Generate the QR code using the injected generator adapter
            $qrCodeImage = $this->qrCodeGenerator->generate($this->user->getAddress(), [
                'size' => '200x200',
            ]);

            // Define file path
            $filename = "qrcode_{$this->user->getId()}.png";
            $storagePath = 'qr_codes/' . $filename;

            // Store the image
            Storage::put($storagePath, $qrCodeImage);

            // Update the user with the file path
            $this->user->setQrCodePath($storagePath);
            $this->userRepository->updateUserQrCode($this->user);

            Log::info("QR code generated and stored for user {$this->user->getId()}");
        } catch (Throwable $e) {
            Log::error("Error generating QR code for user {$this->user->getId()}: {$e->getMessage()}");
            throw $e;
        }
    }
}
