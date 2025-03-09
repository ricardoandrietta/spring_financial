<?php

use App\Infrastructure\Jobs\DeclareWinnerJob;
use App\Infrastructure\Repositories\WinnerEloquentRepository;
use App\Infrastructure\Repositories\UserEloquentRepository;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$declareWinnerJob = new DeclareWinnerJob(new UserEloquentRepository(), new WinnerEloquentRepository());
Schedule::job($declareWinnerJob)->everyFiveMinutes();
