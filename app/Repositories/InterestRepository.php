<?php

namespace App\Repositories;

use App\Contracts\Interfaces\InterestRepositoryInterface;
use App\Models\Interest;
use Illuminate\Support\Collection;

class InterestRepository implements InterestRepositoryInterface
{

    /**
     * Получение всех интересов
     *
     * @return Collection<Interest>
     */
    public function getAllInterests(): Collection
    {
        return Interest::with('children')->get();
    }

    /**
     * Получение "дерева" интересов. От первых элементов-родителей до самого конца вложенности
     *
     * @return Collection<mixed>
     */
    public function getTree(): Collection
    {
        $interests = $this->getAllInterests();
        return $this->buildTree($interests);
    }

    /**
     * Получение списка интересов которые не имею дочерние элементы
     *
     * @return Collection<Interest>
     */
    public function getBaseInterests(): Collection
    {
        // Получаем все интересы, у которых нет дочерних интересов
        return Interest::whereDoesntHave('children')->get();
    }

    /**
     * Получение списка интересов по уровню вложенности
     *
     * @param $level
     * @return Collection
     */
    public function getInterestsByLevel($level)
    {
        $allInterests = Interest::with('parent')->get();

        $interestsAtLevel = $allInterests->filter(function ($interest) use ($level) {
            return $interest->level === $level;
        });

        return $interestsAtLevel->values();
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
