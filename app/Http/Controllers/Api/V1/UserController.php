<?php

namespace App\Http\Controllers\Api\V1;

use App\Transformers\BaseTransformer;
use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class UserController extends BaseController
{
    use Helpers;

    /**
     * index
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->response->item((object)['item'], BaseTransformer::class);
    }
}
