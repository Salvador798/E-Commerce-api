<?php

namespace App\Services;

use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;

class UserLogService
{
    public static function add($action, $module, $description = null, $userId = null)
    {
        $resolvedUserId = $userId ?? Auth::id();

        if (!$resolvedUserId) {
            return;
        }

        UserLog::create([
            'user_id' => $resolvedUserId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip' => request()->ip()
        ]);
    }
}
