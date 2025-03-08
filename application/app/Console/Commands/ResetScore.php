<?php

namespace App\Console\Commands;


use App\Infrastructure\Repositories\UserEloquentRepository;
use Illuminate\Console\Command;


class ResetScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-score';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Resets all user's scores";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $userRepository = new UserEloquentRepository();
            $userRepository->resetScores();
            $this->info('Score has been reset');
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());
            return 1;
        }
    }
}
