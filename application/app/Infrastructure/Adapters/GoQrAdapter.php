<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapters;

use App\Domain\Services\Interfaces\QrCodeAdapterInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoQrAdapter implements QrCodeAdapterInterface
{

    /**
     * API base URL
     *
     * @var string
     */
    protected $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/';

    /**
     * @inheritDoc
     */
    public function generate(string $content, array $options = []): string
    {
        try {
            // Set default options
            $defaultOptions = [
                'size' => '200x200',
            ];

            // Merge with user-provided options
            $options = array_merge($defaultOptions, $options);

            // Build query parameters
            $params = [
                'size' => $options['size'],
                'data' => $content,
            ];

            // Add optional parameters if provided
            if (isset($options['margin'])) {
                $params['margin'] = $options['margin'];
            }

            if (isset($options['error_correction'])) {
                $params['ecc'] = $options['error_correction'];
            }

            // Make the API request
            $response = Http::get($this->apiUrl, $params);

            // Check if request was successful
            if ($response->successful()) {
                return $response->body();
            }

            // Log error and throw exception if unsuccessful
            $errorMessage = "QR code API request failed with status: {$response->status()}";
            Log::error($errorMessage);
            throw new Exception($errorMessage);
        } catch (Exception $e) {
            Log::error("Error generating QR code: {$e->getMessage()}");
            throw $e;
        }
    }
}
