<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\InterestService;
use App\Transformers\BaseTransformer;
use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller as BaseController;

class InterestController extends BaseController
{
    use Helpers;
    public function __construct(
        protected InterestService $interestService
    ) {
    }

    public function index()
    {
        $interestsTree = $this->interestService->getInterestsTree();

        return $this->response->collection($interestsTree, BaseTransformer::class);
    }
}
