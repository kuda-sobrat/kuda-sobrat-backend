<?php

namespace App\Transformers;
use Dingo\Api\Contract\Transformer\Adapter;
use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use Dingo\Api\Transformer\Binding;
use League\Fractal\Manager;
use League\Fractal\TransformerAbstract;

class BaseTransformer implements Adapter
{
    protected $fractal;

    public function __construct(Manager $fractal)
    {
        $this->fractal = $fractal;
    }

    public function transform($response, $transformer, Binding $binding, Request $request)
    {
        return [
            'message' => $binding->getMeta()['message'] ?? null,
            'data' => $response,
            'version' => $request->version(),
        ];
    }
}
