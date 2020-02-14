<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\LogResource;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Repositories\LogRepository;


class LogController extends Controller
{
    protected $log_repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(LogRepository $log_repository)
    {
        $this->middleware('auth');
        $this->log_repository = $log_repository;
    }
    public function index()
    {
        return view('backend.pages.logs.index');
    }
    public function getLogs(Request $request)
    {
        $query = $this->log_repository->getAllLogs($request);
        if ($request->get('per_page') == 0) {
            $logs = $query->get();
        } else {
            $logs = $query
                ->paginate((int) $request->get('per_page', config('axflow.settings.pagination')));
        }
        return LogResource::collection($logs);
    }
    public function destroy(Log $log)
    {
        $log->delete();
    }

    public function destroyBulkLogs(Request $request)
    {
        $logs = $request->ids;

        DB::table('tags')
            ->whereIn('id', $logs)
            ->delete();
    }
}
