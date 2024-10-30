<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\RegisterRequest;
use App\Models\User;
use App\Transformers\BaseTransformer;
use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthController extends BaseController
{
    use Helpers;

    /**
     * Регистрация пользователя.
     *
     * @param RegisterRequest $request
     * @return Response
     */
    public function register(RegisterRequest $request): Response
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->response->item($user, BaseTransformer::class);
    }

    /**
     * Авторизация пользователя
     *
     * @param LoginRequest $request
     * @return Response | UnauthorizedHttpException
     */
    public function login(LoginRequest $request): Response | UnauthorizedHttpException
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new UnauthorizedHttpException('login');
        }

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return $this->response->item((object)['token' => $token, 'user' => $user], BaseTransformer::class);
    }

    /**
     * Получить пользователя
     *
     * @param Request $request
     * @return Response
     */
    public function me(Request $request): Response
    {
        return $this->response->item($request->user(), BaseTransformer::class);
    }

    /**
     * Выход из системы
     *
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request): Response
    {
        $request->user()->tokens()->delete();

        return $this->response->item((object)['message' => 'Вы вышли из системы'], BaseTransformer::class);
    }
}
