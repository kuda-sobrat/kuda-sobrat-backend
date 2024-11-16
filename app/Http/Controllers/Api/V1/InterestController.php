<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Interest;
use App\Services\InterestService;
use App\Transformers\BaseTransformer;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
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

    public function getBaseInterests()
    {
        $baseInterests = $this->interestService->getBaseInterests();

        return $this->response->collection($baseInterests, BaseTransformer::class);
    }

//    // Создание нового интереса
//    public function store(Request $request)
//    {
//        // TODO: Вынести валидацию в Request
//        $this->validate($request, [
//            'name' => 'required|string|max:255',
//            'description' => 'nullable|string',
//            'is_paid' => 'boolean',
//            'parent_id' => 'nullable|exists:interests,id',
//        ]);
//
//        // TODO: Обращение к репозиторию
//        $interest = Interest::create($request->all());
//
//        return $this->response->created(null, $interest);
//    }
//
//    // Обновление интереса
//    public function update(Request $request, $id)
//    {
//        $interest = Interest::findOrFail($id);
//
//        // TODO: Вынести валидацию в Request
//        $this->validate($request, [
//            'name' => 'sometimes|required|string|max:255',
//            'description' => 'nullable|string',
//            'is_paid' => 'boolean',
//            'parent_id' => 'nullable|exists:interests,id',
//        ]);
//
//        // TODO: Обращение к репозиторию
//        $interest->update($request->all());
//
//        return $this->response->noContent();
//    }
//
//    // Удаление интереса
//    public function destroy($id)
//    {
//        // TODO: Обращение к репозиторию
//        $interest = Interest::findOrFail($id);
//        $interest->delete();
//
//        return $this->response->noContent();
//    }
}
