<?php

namespace App\Services;

use App\Contracts\Interfaces\InterestRepositoryInterface;
use Illuminate\Support\Collection;

class InterestService
{
    public function __construct(
      protected InterestRepositoryInterface $interestRepository
    ) {
    }

    public function getInterestsTree(Collection $interests = null): Collection
    {
        // Здесь можно добавить бизнес-логику по обработке данных
        return $this->interestRepository->getTree($interests);
    }

    public function getBaseInterests()
    {
        return $this->interestRepository->getBaseInterests();
    }


}
