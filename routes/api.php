<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

$api = app(\Dingo\Api\Routing\Router::class);

$api->version('v1', ['middleware' => ['api']], function ($api) {
    $api->post('login', [AuthController::class, 'login'])->name('login');
    $api->post('register', [AuthController::class, 'register'])->name('register');
    $api->get('events/getByCoordinates', [\App\Http\Controllers\Api\V1\EventController::class, 'getByCoordinates']);
    $api->get('events/getByInterests', [\App\Http\Controllers\Api\V1\EventController::class, 'getByInterests']);
    $api->get('events/feed', [\App\Http\Controllers\Api\V1\EventController::class, 'feed']);
    $api->get('events/suggestions', [\App\Http\Controllers\Api\V1\EventController::class, 'getSuggestions']);
    $api->get('events/search', [\App\Http\Controllers\Api\V1\EventController::class, 'searchEvents']);
    $api->get('events/{event}', [\App\Http\Controllers\Api\V1\EventController::class, 'show']);
    $api->get('interests', [\App\Http\Controllers\Api\V1\InterestController::class, 'index']);
    $api->get('group/{group}/events', [\App\Http\Controllers\Api\V1\GroupController::class, 'getGroupEvents']);

    $api->group(['middleware' => 'auth:api'], function ($api) {
        $api->get('test', 'App\Http\Controllers\Api\V1\TestController@index');
        $api->get('me', [AuthController::class, 'me']);
        $api->post('logout', [AuthController::class, 'logout']);
    });

    $api->group(['middleware' => ['auth:api']], function ($api) {
        $api->post('interests', [\App\Http\Controllers\Api\V1\InterestController::class, 'store']);
        $api->put('interests/{id}', [\App\Http\Controllers\Api\V1\InterestController::class, 'update']);
        $api->delete('interests/{id}', [\App\Http\Controllers\Api\V1\InterestController::class, 'destroy']);
    });

    // Маршруты для интересов пользователя
    $api->group(['middleware' => 'auth:api'], function ($api) {
        $api->get('user/interests', [\App\Http\Controllers\Api\V1\UserInterestController::class, 'index']);
        $api->post('user/interests', [\App\Http\Controllers\Api\V1\UserInterestController::class, 'store']);
        $api->put('user/interests', [\App\Http\Controllers\Api\V1\UserInterestController::class, 'update']);
        $api->delete('user/interests', [\App\Http\Controllers\Api\V1\UserInterestController::class, 'destroy']);

        $api->get('user/events', [\App\Http\Controllers\Api\V1\EventController::class, 'index']);
        $api->get('user/events/{event}', [\App\Http\Controllers\Api\V1\EventController::class, 'show'])
            ->middleware('record.event.view')
            ->name('events.show');

        $api->get('user/events/{event}/share/{socialNetwork}', [\App\Http\Controllers\Api\V1\EventShareController::class, 'share'])
            ->middleware('record.event.view')
            ->name('events.shares.share');

        $api->get('user/events/{event}/join', [\App\Http\Controllers\Api\V1\EventAttendeeController::class, 'join'])
            ->name('events.join');

        $api->get('user/events/{event}/leave', [\App\Http\Controllers\Api\V1\EventAttendeeController::class, 'leave'])
            ->name('events.leave');
    });

//    Примеры маршрутизации
//    $api->group(['prefix' => 'users'], function ($api) {
//        $api->get('/', 'App\Http\Controllers\UserController@index')->name('users.index');
//        $api->get('/{id}', 'App\Http\Controllers\UserController@show')->name('users.show');
//        $api->post('/', 'App\Http\Controllers\UserController@store')->name('users.store');
//        $api->put('/{id}', 'App\Http\Controllers\UserController@update')->name('users.update');
//        $api->delete('/{id}', 'App\Http\Controllers\UserController@destroy')->name('users.destroy');
//    });
//
//    Ресурсы
//
//    $api->resource('users', 'App\Http\Controllers\Api\V1\UserController', [
//        'except' => ['destroy'], // Исключаем метод destroy
//        'names' => [
//            'index' => 'api.users.index',
//            'show' => 'api.users.show',
//            'store' => 'api.users.store',
//            'update' => 'api.users.update',
//        ],
//    ]);
//
//    // Добавьте дополнительный маршрут, например, для поиска
//    $api->get('users/search', 'App\Http\Controllers\Api\V1\UserController@search')->name('api.users.search');
});
