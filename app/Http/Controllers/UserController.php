<?php

namespace App\Http\Controllers;

use App\Dto\UserDto;
use App\Enums\ErrorEnum;
use App\Models\LoginToken;
use App\Models\User;
use App\Services\UserService;
use App\Validators\UserValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * @var UserDto
     */
    private UserDto $userDto;

    public function __construct(UserDto $userDto)
    {
        $this->userDto = $userDto;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createUser(Request $request): JsonResponse
    {
        $this->userDto->attachValues($request->all());
        $validatePayload = UserValidator::validateCreateAccount($this->userDto);

        if($validatePayload['error']) {
            return response()->json($validatePayload, Response::HTTP_NOT_ACCEPTABLE);
        }
        User::create([
            'name' => $this->userDto->name,
            'email' => $this->userDto->email,
            'password' => md5($this->userDto->password),
        ]);

        return response()->json($validatePayload, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $this->userDto->attachValues($request->all());
        $isLoginValid = UserValidator::validateLogin($this->userDto);

        if (!$isLoginValid) {
            return response()->json([
                'error' => true,
                'message' => ErrorEnum::LOGIN_FAIL,
            ]);
        }
        $token = UserService::getToken();
        $user = UserService::getUserByEmail($this->userDto->email);
        LoginToken::create([
            'token' => $token,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'error' => false,
            'token' => $token,
        ]);
    }
}
