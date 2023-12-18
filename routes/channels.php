<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Group;


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

Broadcast::channel('Group.{groupToken}', function ($user, $groupToken) {
    // Vérifier si l'utilisateur appartient au groupe
    $group = Group::where('token', $groupToken)->first();

    if ($group && $user->groups()->where('group_id', $group->id)->exists()) {
        return true; // L'utilisateur appartient au groupe
    }

    return false; // L'utilisateur n'appartient pas au groupe
}, ['guards' => ['sanctum']]);
