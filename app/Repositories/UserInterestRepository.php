<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;

class UserInterestRepository
{
    /**
     * Получение интересов пользователя с дочерними интересами.
     */
    public function getAll(User $user): Collection
    {
        return $user->interests()->with('children')->get();
    }

    /**
     * Добавление интересов пользователю.
     */
    public function attach(User $user, array $interestIds): void
    {
        $user->interests()->attach($interestIds);
    }

    /**
     * Обновление интересов пользователя.
     */
    public function sync(User $user, array $interestIds): void
    {
        $user->interests()->sync($interestIds);
    }

    /**
     * Удаление интересов пользователя.
     */
    public function detach(User $user, array $interestIds): void
    {
        $user->interests()->detach($interestIds);
    }
}
