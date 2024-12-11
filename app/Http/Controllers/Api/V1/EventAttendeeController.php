<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\SocialNetwork;
use App\Repositories\EventParticipationService;
use App\Transformers\BaseTransformer;
use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventAttendeeController extends Controller
{
    use Helpers;

    public function __construct(
        protected EventParticipationService $participationService,
    ) {
    }

    /**
     * Присоединение пользователя к мероприятию.
     *
     * @param Event $event
     * @return Response
     */
    public function join(Event $event): Response
    {
        $user = Auth::user();

        try {
            $this->participationService->join($event, $user);

            return $this->response->item(null, BaseTransformer::class, ['message' => 'Пользователь успешно добавлен к участникам мероприятия']);
        } catch (Exception $e) {
            $this->response->error($e->getMessage(), 400);
        }
    }

    /**
     * Снятие участия пользователя в мероприятии.
     *
     * @param Event $event
     * @return Response
     */
    public function leave(Event $event): Response
    {
        $user = Auth::user();

        try {
            $this->participationService->leave($event, $user);

            return $this->response->item(null, BaseTransformer::class, ['message' => 'Пользователь успешно снят с участия в мероприятии']);
        } catch (Exception $e) {
            $this->response->error($e->getMessage(), 400);
        }
    }
}
