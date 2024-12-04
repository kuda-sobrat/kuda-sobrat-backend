<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\EventRepository;
use App\Transformers\BaseTransformer;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    use Helpers;

    public function __construct(
        public EventRepository $repository,
    ) {
    }

    /**
     * Получение интересов текущего пользователя
     *
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        $events = $this->repository->getAll();

        return $this->response->collection($events, BaseTransformer::class);
    }
}
