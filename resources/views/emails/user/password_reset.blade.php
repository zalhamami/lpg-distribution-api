@extends('layouts.email')

@section('title', 'Reset Password')

@section('email.body')
    <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0">
        <tr>
            <td class="content-cell">
                <h1>Reset Password</h1>
                <p>You are receiving this email because we received a password reset request for your account..</p>
                <table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center">
                            <div>
                                <a href="{{ $action_url }}" class="button">Reset Password</a>
                            </div>
                        </td>
                    </tr>
                </table>
                <p>This password reset link will expire in 60 minutes.</p>
                <p>If you did not request a password reset, no further action is required.</p>
                <p>Best Regards,<br>App</p>
                <table class="body-sub">
                    <tr>
                        <td>
                            <p class="sub">If you’re having trouble clicking the button, copy and paste the URL below into your web browser.
                            </p>
                            <p class="sub"><a href="{{ $action_url }}">{{ $action_url }}</a></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection
