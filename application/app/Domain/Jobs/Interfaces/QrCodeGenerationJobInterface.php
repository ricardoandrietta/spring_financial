<?php

namespace App\Domain\Jobs\Interfaces;

use Throwable;

interface QrCodeGenerationJobInterface
{
    /**
     * Execute the job to generate and store a QR code.
     *
     * @throws Throwable
     *
     * @return void
     */
    public function handle(): void;
}
