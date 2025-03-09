<?php

namespace App\Domain\Services\Interfaces;

interface QrCodeAdapterInterface
{
    /**
     * Generate a QR code image for the given content.
     *
     * @param string $content The content to encode in the QR code
     * @param array $options Additional options (size, margin, etc.)
     * @return string Binary content of the QR code image
     *
     * @throws \Exception If QR code generation fails
     */
    public function generate(string $content, array $options = []): string;
}
