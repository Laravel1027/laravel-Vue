@extends('email')
@section('content')
    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="margin-bottom: 0px;">
        <tr>
            <td style="padding: 40px; padding-bottom: 0px; font-family: sans-serif; font-size: 20px; line-height: 27px; color: #666666; font-size: 16px">
                Hi {{$name}},
            </td>
        </tr>
        <tr>
            <td style="padding: 40px; padding-top: 10px; font-family: sans-serif; font-size: 20px; line-height: 27px; color: #666666; font-size: 14px; text-align: justify">
                @if ($issue->status === 'todo')
                    There is a new comment in project <b>{{$project->name}}</b>.
                    under todo status.
                @elseif ($issue->status === 'review')
                    Issue {{$issue->label}} in the project <b>{{$project->name}}</b> is 
                    in review status.
                @else
                    Issue {{$issue->label}} in the project <b>{{$project->name}}</b> is 
                    approved.
                @endif
                Click the link below to view the details. <br>
                <br>
            </td>
        </tr>
        <tr>
        <tr>
            <td style="padding: 40px; padding-top: 10px; font-family: sans-serif; font-size: 20px; line-height: 27px; color: #666666; font-size: 14px; text-align: justify">
                <b>Project:</b> {{$project->name}}<br>
                <b>Company:</b> {{$project->company}}<br>
                <b>Sent by:</b> Prooflo Team<br>
                <b>Issue Number:</b> {{$issue->label}}
            </td>
        </tr>
        <tr>
            <td valign="middle" align="center" style="-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background-image: url(http://tedgoas.github.io/Cerberus/assets/bg-btn.png); background-position: top left; background-repeat: repeat-x; background-color: #fff;padding-bottom: 30px">
                <a href="{{config('app.url')}}/loadProofer/{{$project->project_hash}}/{{$issue->proof->revision->id}}/{{$issue->proof_id}}/{{$issue->id}}" target="blank" style="color: #ffffff; font-family: sans-serif; font-size: 15px; line-height: 15px; text-align: center; text-decoration: none; display: block; padding: 15px 20px; border: 1px solid #333333; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background-color: #575959; width: 130px; border: 0px">
                    <b>GO TO PROOFER</b>
                </a>
            </td>
        </tr>
        <tr>
            <td valign="middle" align="center" style="padding: 0 0 25px 0;">
                <a href="{{config('app.url')}}/settings/notifications" target="blank" style="text-decoration: none; font-size: 11px; color: #80b4a0;">
                    <b>Unsubscribe from Prooflo Emails</b>
                </a>
            </td>
        </tr>
    </table>
@endsection