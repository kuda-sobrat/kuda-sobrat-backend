<?php

namespace App\Services;

use App\Contracts\Interfaces\InterestRepositoryInterface;

class InterestService
{
    public function __construct(
      protected InterestRepositoryInterface $interestRepository
    ) {
    }

    public function getInterestsTree()
    {
        // Здесь можно добавить бизнес-логику по обработке данных
        return $this->interestRepository->getTree();
    }
}
