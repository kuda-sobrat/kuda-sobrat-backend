<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Interfaces\EventShareRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\SocialNetwork;
use App\Services\Events\EventShareService;
use App\Transformers\BaseTransformer;
use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;

class EventShareController extends Controller
{
    use Helpers;

    public function __construct(
        public EventShareService $service,
        public EventShareRepositoryInterface $repository,
    ) {
    }

    // TODO: Формировать ссылку (связанную с пользователем). При переходе по ссылке инкрементировать счетчик поделившихся (если перешел первый раз)
    public function share(Request $request, Event $event, SocialNetwork $socialNetwork): Response
    {
        // Вызываем сервис для обработки действия
        try {
            $this->service->shareEvent($event, $socialNetwork);
            return $this->response->item(null, BaseTransformer::class);
        } catch (\InvalidArgumentException $e) {
            return $this->response->errorBadRequest($e->getMessage());
        }
    }
}
