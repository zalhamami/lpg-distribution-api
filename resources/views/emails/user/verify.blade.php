@extends('layouts.email')

@section('title', 'Verify your email address')

@section('email.body')
    <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0">
        <tr>
            <td class="content-cell">
                <h1>Verify your email address</h1>
                <p>Thanks for signing up for App! We're excited to have you as an early user.</p>
                <!-- Action -->
                <table class="body-action" align="center" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center">
                            <div>
                                <a href="{{ $action_url }}" class="button">Verify Email</a>
                            </div>
                        </td>
                    </tr>
                </table>
                <p>Best Regards,<br>App Team</p>
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
