<?php

namespace App\Contracts\Interfaces;

use App\Models\Interest;
use Illuminate\Support\Collection;

interface InterestRepositoryInterface
{
    /**
     * @return Collection<Interest>
     */
    public function getAllInterests(): Collection;

    /**
     * @return Collection<Interest>
     */
    public function getTree(): Collection;
}
