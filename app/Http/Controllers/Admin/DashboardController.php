<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SearchLog;

class DashboardController extends Controller
{
    public function searchLogs(Request $request)
    {
        $perPage = 20;

        // If using Meilisearch + Scout
        $query = $request->input('q');

        if ($query) {
            $logs = SearchLog::search($query)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        } else {
            $logs = SearchLog::orderBy('created_at', 'desc')->paginate($perPage);
        }

        return view('admin.search.index', compact('logs', 'query'));
    }
}
