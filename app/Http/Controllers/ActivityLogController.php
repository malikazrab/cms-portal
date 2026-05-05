<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = ActivityLog::with('user')
            ->when($request->filled('action'), fn ($query) => $query->where('action', 'like', '%'.$request->string('action').'%'))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.activity-logs.index', compact('logs'));
    }
}
