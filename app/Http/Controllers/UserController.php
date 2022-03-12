<?php

namespace App\Http\Controllers;

use App\Enums\ErrorEnum;
use App\Enums\SuccessEnum;
use App\Models\LoginToken;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createUser(Request $request): JsonResponse
    {
//        $this->validate($request, [
//            'name' => 'required',
//            'email' => 'required|unique:users',
//            'password' => 'required',
//            'confirmPassword' => 'require',
//        ]);

        if ($request->get('password') == $request->get('confirmPassword')) {
            return response()->json(
                [
                    'error' => true,
                    'message' => ErrorEnum::PASSWORDS_DOESNT_MATCH
                ],
                Response::HTTP_NOT_ACCEPTABLE
            );
        }

        User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => md5($request->get('password')),
        ]);

        return response()->json(
            [
                'error' => false,
                'message' => SuccessEnum::USER_CREATED,
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
//        $this->validate($request, [
//            'email' => 'required',
//            'password' => 'required',
//        ]);
        dd('aq');

        $user = UserService::getUserByEmail($request->get('email'));
        if (is_null($user)) {
            return response()->json(
                [
                    'error' => true,
                    'message' => ErrorEnum::PASSWORDS_DOESNT_MATCH,
                ],
                Response::HTTP_NOT_ACCEPTABLE
            );
        }

        if (md5($request->get('password') != $user->password)) {
            return response()->json(
                [
                    'error' => true,
                    'message' => ErrorEnum::PASSWORDS_DOESNT_MATCH,
                ],
                Response::HTTP_NOT_ACCEPTABLE
            );
        }
        $token = UserService::getToken();
        LoginToken::create([
            'user_id' => $user->id,
            'token' => $token,
        ]);

        return response()->json(
            [
                'error' => false,
                'token' => UserService::getToken(),
            ],
            Response::HTTP_NOT_ACCEPTABLE
        );
    }
}
