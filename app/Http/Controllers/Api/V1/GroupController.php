<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\EventGroup;
use App\Transformers\BaseTransformer;
use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller as BaseController;

class GroupController extends BaseController
{
    use Helpers;

    /**
     * @param EventGroup $group
     * @return Response
     */
    public function getGroupEvents(EventGroup $group): Response
    {
        // Получаем мероприятия группы
        $events = $group->events;

        // Возвращаем мероприятия в формате JSON
        return $this->response->collection(collect([
            'group' => $group,
        ]), BaseTransformer::class);
    }
}
