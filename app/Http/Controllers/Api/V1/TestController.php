<?php

namespace App\Http\Controllers\Api\V1;

use App\Transformers\BaseTransformer;
use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class TestController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, Helpers;

    /**
     * Тестовый эндпоинт
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $data = (object)['hello' => 'hello'];
        return $this->response->item($data, BaseTransformer::class);
    }
}
