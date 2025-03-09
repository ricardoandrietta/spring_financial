<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\User;
use App\Models\User as UserModel;

class UserEloquentRepository implements \App\Domain\Repositories\Interfaces\UserRepositoryInterface
{

    public function resetScores(): void
    {
        UserModel::where('score', '>', 0)->update(['score' => 0]);
    }

    /**
     * Get all users ordered by score
     *
     * @param string $orderDirection
     * @return array|User[]
     */
    public function getAllOrderedByScore(string $orderDirection = 'desc'): array
    {
        $users = UserModel::orderBy('score', $orderDirection)
            ->orderBy('name', 'asc')
            ->get();

        return $users->map(function ($userModel) {
            return $this->mapToEntity($userModel);
        })->toArray();
    }

    /**
     * Find user by ID
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        $userModel = UserModel::find($id);

        if (!$userModel) {
            return null;
        }

        return $this->mapToEntity($userModel);
    }

    /**
     * Create a new user
     *
     * @param User $user
     * @return User
     */
    public function create(User $user): User
    {
        $userModel = new UserModel([
            'name' => $user->getName(),
            'age' => $user->getAge(),
            'score' => $user->getScore(),
            'address' => $user->getAddress(),
        ]);

        $userModel->save();

        // Trigger leaderboard update event
//        event(new LeaderboardUpdated());

        return $this->mapToEntity($userModel);
    }

    /**
     * Update an existing user
     *
     * @param User $user
     * @return User
     */
    public function update(User $user): User
    {
        $userModel = UserModel::findOrFail($user->getId());

        $userModel->update([
            'name' => $user->getName(),
            'age' => $user->getAge(),
            'score' => $user->getScore(),
            'address' => $user->getAddress(),
        ]);

        // Trigger leaderboard update event
//        event(new LeaderboardUpdated());

        return $this->mapToEntity($userModel);
    }

    /**
     * Delete a user by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $userModel = UserModel::find($id);

        if (!$userModel) {
            return false;
        }

        $result = $userModel->delete();

        // Trigger leaderboard update event
//        event(new LeaderboardUpdated());

        return $result;
    }

    /**
     * Add points to a user
     *
     * @param int $userId
     * @param int $points
     * @return User|null
     */
    public function addPoints(int $userId, int $points): ?User
    {
        $userModel = UserModel::find($userId);

        if (!$userModel) {
            return null;
        }

        $userModel->score += $points;
        $userModel->save();

        // Trigger leaderboard update event
//        event(new LeaderboardUpdated());

        return $this->mapToEntity($userModel);
    }

    /**
     * Subtract points from a user
     *
     * @param int $userId
     * @param int $points
     * @return User|null
     */
    public function subtractPoints(int $userId, int $points): ?User
    {
        $userModel = UserModel::find($userId);

        if (!$userModel) {
            return null;
        }

        $userModel->score -= $points;
        if ($userModel->score < 0) {
            $userModel->score = 0;
        }
        $userModel->save();

        // Trigger leaderboard update event
//        event(new LeaderboardUpdated());

        return $this->mapToEntity($userModel);
    }

    /**
     * Map a UserModel to a User entity
     *
     * @param UserModel $userModel
     * @return User
     */
    private function mapToEntity(UserModel $userModel): User
    {
        return new User(
            name: $userModel->name,
            age: $userModel->age,
            score: $userModel->score,
            address: $userModel->address,
            id: $userModel->id
        );
    }
}
