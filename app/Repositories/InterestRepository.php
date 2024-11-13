<?php

namespace App\Repositories;

use App\Contracts\Interfaces\InterestRepositoryInterface;
use App\Enums\ProcessStatusEnum;
use App\Models\Community;
use App\Models\ContextPost;
use App\Models\Interest;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InterestRepository implements InterestRepositoryInterface
{

    public function getAllInterests(): Collection
    {
        return Interest::with('children')->get();
    }

    public function getTree(): Collection
    {
        $interests = $this->getAllInterests();
        return $this->buildTree($interests);
    }

    /**
     * @param $interests
     * @param $parentId
     * @return Collection<mixed>
     */
    private function buildTree($interests, $parentId = null): Collection
    {
        $branch = new Collection();

        foreach ($interests as $interest) {
            $parents = $interest->parent->pluck('id')->toArray();
            if (($parentId === null && empty($parents)) || in_array($parentId, $parents)) {
                $children = $this->buildTree($interests, $interest->id);

                $branch->push((object)[
                    'attributes' => [
                        'id' => $interest->id,
                        'name' => $interest->name,
                        'description' => $interest->description,
                        'is_paid' => $interest->is_paid,
                        // Добавьте другие свойства при необходимости
                    ],
                    'childs' => $children,
                ]);
            }
        }

        return $branch;
    }
}
