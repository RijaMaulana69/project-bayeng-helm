<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $startDate = $request->input('start_date', $request->input('date_from'));
        if (!empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        $endDate = $request->input('end_date', $request->input('date_to'));
        if (!empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $logs = $query->paginate(20)->withQueryString();

        $actions = ActivityLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin.activity-log.index', compact('logs', 'actions'));
    }
}
