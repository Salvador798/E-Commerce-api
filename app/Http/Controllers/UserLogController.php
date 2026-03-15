<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLogController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => UserLog::with('user:id,name,email')
                ->latest()
                ->paginate(20)
        ]);
    }
}
