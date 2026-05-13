<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = DB::table('sessions')
            ->orderBy('last_activity', 'desc')
            ->get();

        return view('admin.sessions.index', compact('sessions'));
    }
}
