<?php

namespace App\Domain\Repositories\Interfaces;

use App\Domain\Entities\Winner;

interface WinnerRepositoryInterface
{
    /**
     * Create a new winner record
     *
     * @param Winner $winner
     * @return Winner
     */
    public function create(Winner $winner): Winner;
}
