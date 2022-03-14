<?php

namespace App\Http\Middleware;

use App\Models\LoginToken;
use App\Models\User;
use App\Singleton\UserSingleton;
use Carbon\Carbon;
use Closure;
use Exception;

class UserAuth
{
    public const MAX_TIME_MINUTES_TOKEN = 1000 * 60 * 4;

    /**
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        $accessToken = $request->input('x-access-token');
        $loginToken = LoginToken::query()
            ->where('token', $accessToken)
            ->first();

        if (is_null($loginToken)) {
            throw new Exception('Login expirado!');
        }
        $timeNow = Carbon::now();
        $timeOfTokenIsCreated = $timeNow->diffInMinutes($loginToken->created_at);

        if($timeOfTokenIsCreated >= self::MAX_TIME_MINUTES_TOKEN) {
            throw new Exception('Login expirado!');
        }
        $user = User::query()->find($loginToken->user_id);
        UserSingleton::setUser($user);

        return $next($request);
    }

}
