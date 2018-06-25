<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserNotificationsController extends Controller
{
    public function index(User $user)
    {
        return auth()->user()->unreadNotifications;
    }

    /**
     * @param User $user
     * @param $notificationId
     */
    public function destroy(User $user, $notificationId)
    {
        auth()->user()->notifications()->findOrFail($notificationId)->markAsRead();
    }
}
