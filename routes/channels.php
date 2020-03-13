<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('pdf-uploaded', function ($user) {
    return Auth::check();
});

Broadcast::channel('pdf-readed', function ($user) {
    return Auth::check();
});

Broadcast::channel('pdf-converting', function ($user) {
    return Auth::check();
});
