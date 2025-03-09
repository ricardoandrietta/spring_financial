<?php

namespace App\Domain\Repositories\Interfaces;
use App\Domain\Entities\User;

interface UserRepositoryInterface
{
    public function resetScores(): void;

    /**
     * Get all users ordered by score
     *
     * @param string $orderDirection
     * @return array<User>
     */
    public function getAllOrderedByScore(string $orderDirection = 'desc'): array;

    /**
     * Find user by ID
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     * Create a new user
     *
     * @param User $user
     * @return User
     */
    public function create(User $user): User;

    /**
     * Update an existing user
     *
     * @param User $user
     * @return User
     */
    public function update(User $user): User;

    /**
     * Sets the QR code path for the user
     *
     * @param User $user
     * @return User
     */
    public function updateUserQrCode(User $user): User;

    /**
     * Delete a user by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Add points to a user
     *
     * @param int $userId
     * @param int $points
     * @return User|null
     */
    public function addPoints(int $userId, int $points): ?User;

    /**
     * Subtract points from a user
     *
     * @param int $userId
     * @param int $points
     * @return User|null
     */
    public function subtractPoints(int $userId, int $points): ?User;

    /**
     * Return users by score
     *
     * @return array
     */
    public function getUsersGroupedByScore(): array;
}
