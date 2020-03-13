@extends('email')
@section('content')
    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="margin-bottom: 0px;">
        <tr>
            <td style="padding: 40px; padding-top: 10px; font-family: sans-serif; font-size: 20px; line-height: 27px; color: #666666; font-size: 14px; text-align: justify">
                Hello! You are receiving this email because we received a password reset request for your account.
                <br>
            </td>
        </tr>
        <tr>
            <td valign="middle" align="center" style="-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background-image: url(http://tedgoas.github.io/Cerberus/assets/bg-btn.png); background-position: top left; background-repeat: repeat-x; background-color: #fff;padding-bottom: 30px">
                <a href="{{config('app.url')}}/password/reset/{{$token}}" target="blank" style="color: #ffffff; font-family: sans-serif; font-size: 13px; line-height: 15px; text-align: center; text-decoration: none; display: block; padding: 15px 20px; border: 1px solid #80b4a0; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background-color: #80b4a0; width: 150px; border: 0px">
                    <b>RESET PASSWORD</b>
                </a>
            </td>
        </tr>
        <tr>
            <td style="padding: 40px; padding-top: 10px; font-family: sans-serif; font-size: 20px; line-height: 27px; color: #666666; font-size: 14px; text-align: justify">
                If you did not request a password reset, no further action is required. <br>
                Regards, <br>
                Prooflo
            </td>
        </tr>
        <hr>
        <tr>
            <td style="padding: 40px; padding-top: 10px; font-family: sans-serif; font-size: 20px; line-height: 27px; color: #666666; font-size: 14px; text-align: justify">
                <small>
                    If you’re having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:
                    <br>
                    {{config('app.url')}}/password/reset/{{$token}}
                </small>
            </td>
        </tr>
    </table>
@endsection