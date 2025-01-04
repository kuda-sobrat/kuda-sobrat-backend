<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\DestroyUserInterestsRequest;
use App\Http\Requests\V1\StoreUserInterestsRequest;
use App\Http\Requests\V1\UpdateUserInterestsRequest;
use App\Models\User;
use App\Repositories\UserInterestRepository;
use App\Services\InterestService;
use App\Transformers\BaseTransformer;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Auth;

class UserInterestController extends Controller
{
    use Helpers;

    public function __construct(
        protected InterestService $service,
        protected UserInterestRepository $repository,
    ) {
    }

    /**
     * Получение интересов текущего пользователя
     *
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $interests = $this->repository->getAll($user);

        return $this->response->collection($interests, BaseTransformer::class);
    }

    /**
     * Добавление интересов пользователю
     *
     * @param StoreUserInterestsRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function store(StoreUserInterestsRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $this->repository->attach($user, $request->interest_ids);

        return $this->response->created();
    }

    /**
     * Обновление интересов пользователя
     *
     * @param UpdateUserInterestsRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function update(UpdateUserInterestsRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $this->repository->sync($user, $request->interest_ids);

        return $this->response->noContent();
    }

    /**
     * Удаление интересов пользователя
     *
     * @param DestroyUserInterestsRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function destroy(DestroyUserInterestsRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $this->repository->detach($user, $request->interest_ids);

        return $this->response->noContent();
    }
}
