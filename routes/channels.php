<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('teachers.{teacherId}', function ($user, $teacherId) {
    return $user->teacher !== null
        && (int) $user->teacher->id === (int) $teacherId;
});
