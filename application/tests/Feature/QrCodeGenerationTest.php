<?php


use App\Domain\Entities\User;
use App\Domain\Jobs\Interfaces\QrCodeGenerationJobInterface;
use App\Domain\Repositories\Interfaces\UserRepositoryInterface;
use App\Domain\Services\Interfaces\QrCodeAdapterInterface;
use App\Domain\Services\QrCodeService;
use App\Infrastructure\Adapters\GoQrAdapter;
use App\Infrastructure\Jobs\GenerateUserQrCodeJob;
use App\Infrastructure\Repositories\UserEloquentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a test user
    $this->user = new User(
        name: 'John',
        age: 25,
        score: 85,
        address: '123 Test Street, Test City, 12345',
        qrCodePath: null,
        id: 1
    );

    // Create a mock repository that will return the domain entity
    $this->repository = Mockery::mock(UserRepositoryInterface::class);
    $this->repository->shouldReceive('findById')
        ->with(1)
        ->andReturn($this->user);

    $this->repository->shouldReceive('update')
        ->with(Mockery::type(User::class))
        ->andReturn(true);

    $this->repository->shouldReceive('updateUserQrCode')
        ->with(Mockery::type(User::class))
        ->andReturn($this->user);

    // Mock storage
    Storage::fake('local');

    // Ensure the qr_codes directory exists
    Storage::makeDirectory('qr_codes');
});

test('qr code service dispatches job when called', function () {
    // Arrange
    Queue::fake();
    $qrCodeService = new QrCodeService();

    // Act
    $qrCodeService->generateQrCodeForUserAddress($this->user, $this->repository);

    // Assert
    Queue::assertPushed(GenerateUserQrCodeJob::class, function ($job) {
        return $job->getUserId() === $this->user->getId();
    });
});

test('job generates and stores qr code successfully with adapter', function () {
    // Arrange - Create a mock QR code generator
    $mockQrCodeAdapter = Mockery::mock(QrCodeAdapterInterface::class);
    $mockQrCodeAdapter->shouldReceive('generate')
        ->once()
        ->with($this->user->getAddress(), Mockery::any())
        ->andReturn('fake-image-content');

    $this->app->instance(QrCodeGenerationJobInterface::class, $mockQrCodeAdapter);

    $job = new GenerateUserQrCodeJob($this->user, $this->repository, $mockQrCodeAdapter);

    // Act
    $job->handle();

    // Assert
    $expectedPath = "qr_codes/qrcode_{$this->user->getId()}.png";

    // Check if file was stored
    Storage::assertExists($expectedPath);
});

// Test for QrServerApiAdapter
test('QrServerApiAdapter calls the correct API and returns the response body', function () {
    // Skip this test in a real environment - we'll mock the HTTP call
    Http::fake([
        'api.qrserver.com/*' => Http::response('fake-qr-image', 200),
    ]);

    $adapter = new GoQrAdapter();
    $result = $adapter->generate('Test content', ['size' => '200x200']);

    expect($result)->toBe('fake-qr-image');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'https://api.qrserver.com/v1/create-qr-code/') &&
            $request['data'] == 'Test content' &&
            $request['size'] == '200x200';
    });
});
