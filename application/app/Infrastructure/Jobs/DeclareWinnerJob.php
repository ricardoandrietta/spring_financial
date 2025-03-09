<?php

declare(strict_types=1);

namespace App\Infrastructure\Jobs;

use App\Application\UseCases\Winner\DeclareWinnerUseCase;
use App\Domain\Repositories\Interfaces\UserRepositoryInterface;
use App\Domain\Repositories\Interfaces\WinnerRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeclareWinnerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected WinnerRepositoryInterface $winnerRepository
    )
    {
        Log::debug('DeclareWinnerJob:construct');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug('DeclareWinnerJob:handle');
        $declareWinnerUseCase = new DeclareWinnerUseCase($this->userRepository, $this->winnerRepository);
        $winner = $declareWinnerUseCase->execute();

        if ($winner) {
            Log::info('New winner declared', [
                'user_id' => $winner->getUserId(),
                'score' => $winner->getScore(),
                'time' => $winner->getCreatedAt()->format('Y-m-d H:i:s')
            ]);
        } else {
            Log::info('No winner declared due to a tie for the highest score');
        }
    }
}
