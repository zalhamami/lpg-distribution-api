<?php

namespace App\Http\Controllers;

use App\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends ApiController
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * AuthController constructor.
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $user = $this->userRepo->getByField('email', $request['email']);
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials)) {
            return $this->errorResponse('These credentials do not match our records', 401);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            return $this->errorResponse('Please verify your email first before login.', 400);
        }

        $response = $this->createAccessToken($user);
        return $this->singleData($response);
    }

    /**
     * @param User $user
     * @return array
     */
    private function createAccessToken(User $user)
    {
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

        return [
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ];
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAuth()
    {
        $response = DB::table('oauth_access_tokens')
                        ->where('revoked', 0)
                        ->orderByDesc('created_at')
                        ->first();
        return $this->singleData([
            'expires_at' => $response->expires_at
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeUserToken(Request $request)
    {
        $request->user()->token()->revoke();

        return $this->successResponse([
            'message' => 'Successfully revoked'
        ]);
    }
}
