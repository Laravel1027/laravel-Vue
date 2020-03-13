Hi!

<br><br>

{{ $invitation->team->owner->name }} has invited you to join their {{ Spark::teamString() }}!

<br><br>

Since you already have an account, you may accept the invitation from your
account settings screen.

<br><br>

<a href="{{config('app.url')}}/settings/teams" target="blank"
   style="color: #ffffff; font-family: sans-serif; font-size: 15px; line-height: 15px; text-align: center; text-decoration: none; display: block; padding: 15px 20px; border: 1px solid #333333; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background-color: #575959; width: 150px; border: 0px">
    <b>ACCEPT INVITATION</b>
</a>

<br><br>

See you soon!
