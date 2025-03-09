<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Models\Winner as WinnerModel;
use App\Domain\Entities\Winner;
use App\Domain\Repositories\Interfaces\WinnerRepositoryInterface;
use DateTime;

class WinnerEloquentRepository implements WinnerRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function create(Winner $winner): Winner
    {
        $winnerModel = new WinnerModel([
            'user_id' => $winner->getUserId(),
            'score' => $winner->getScore(),
            'created_at' => $winner->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);

        $winnerModel->save();
        return $this->mapToEntity($winnerModel);
    }

    /**
     * Map a WinnerModel to a Winner entity
     *
     * @param WinnerModel $winnerModel
     * @return Winner
     */
    private function mapToEntity(WinnerModel $winnerModel): Winner
    {
        return new Winner(
            userId: $winnerModel->user_id,
            score: $winnerModel->score,
            createdAt: isset($winnerModel->created_at) ? DateTime::createFromFormat('Y-m-d H:i:s', $winnerModel->created_at) : null,
            id: $winnerModel->id
        );
    }
}
