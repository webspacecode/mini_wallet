<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function getUsers(Request $request)
    {
        return response()->json([
            'message' => 'Fetched users successful.',
            'users' => $request->user()->users()->paginate(10)
        ]);
    }
}
