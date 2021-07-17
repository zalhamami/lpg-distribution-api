<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class EmailVerificationController extends ApiController
{
    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function verify(Request $request, int $id)
    {
        if (!$request->hasValidSignature()) {
            return $this->errorResponse('Invalid/Expired url provided.', 401);
        }

        $user = User::findOrFail($id);
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect(env('VERIFICATION_EMAIL_REDIRECT'));
    }

    /**
     * @param string $value
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(string $value)
    {
        $user = User::whereEmail($value)->firstOrFail();
        if ($user->hasVerifiedEmail()) {
            return $this->errorResponse('Email already verified.', 400);
        }
        $user->sendEmailVerificationNotification();
        return $this->successResponse([
            'message' => 'Email verification link sent on your email.',
        ]);
    }
}
