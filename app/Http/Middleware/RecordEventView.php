<?php

namespace App\Http\Middleware;

use App\Models\Event;
use App\Services\EventViewService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordEventView
{
    public function __construct(
      protected EventViewService $eventViewService,
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
//        // Выполняем следующий middleware или контроллер
//        $response = $next($request);

        // Получаем идентификатор события из параметров маршрута
        $event = $request->route('event');

        if ($event instanceof Event) {
            // Записываем просмотр мероприятия
            $this->eventViewService->recordView($event);

        }

        return $next($request);
    }
}
