<?php

namespace Database\Factories;

use App\Domain\Repositories\Interfaces\UserRepositoryInterface;
use App\Domain\Services\Interfaces\QrCodeServiceInterface;
use App\Domain\Services\QrCodeService;
use App\Infrastructure\Repositories\UserEloquentRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    protected UserRepositoryInterface $userRepository;
    protected QrCodeServiceInterface $qrCodeGenerator;

    public function __construct(
        $count = null,
        ?Collection $states = null,
        ?Collection $has = null,
        ?Collection $for = null,
        ?Collection $afterMaking = null,
        ?Collection $afterCreating = null,
        $connection = null,
        ?Collection $recycle = null,
        bool $expandRelationships = true
    ) {
        parent::__construct(
            $count,
            $states,
            $has,
            $for,
            $afterMaking,
            $afterCreating,
            $connection,
            $recycle,
            $expandRelationships
        );

        $this->userRepository = new UserEloquentRepository();
        $this->qrCodeGenerator = new QrCodeService();
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'age' => fake()->numberBetween(15, 45),
            'score' => fake()->numberBetween(3, 9),
            'address' => fake()->address(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            $this->qrCodeGenerator->generateQrCodeForUserAddress($this->userRepository->mapToEntity($user), $this->userRepository);
        });
    }
}
