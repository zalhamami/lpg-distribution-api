<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Client\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords, ApiResponser;

    /**
     * @param Request $request
     * @param $response
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetResponse(Request $request, $response)
    {
        return $this->successResponse([
            'message' => $response,
        ]);
    }

    /**
     * @param Request $request
     * @param $response
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetFailedResponse(Request $request, $response)
    {
        $statusCode = 500;
        if ($response == 'passwords.user') {
            $response = 'User not found.';
            $statusCode = 404;
        }
        if ($response == 'passwords.token') {
            $response = 'Token mismatch.';
            $statusCode = 400;
        }
        return $this->errorResponse($response, $statusCode);
    }
}
