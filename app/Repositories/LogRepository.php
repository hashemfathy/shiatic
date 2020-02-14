<?php

namespace App\Repositories;

use App\Models\Log;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;


class LogRepository
{
    public function getAllLogs(Request $request)
    {
        return QueryBuilder::for(Log::class)->with('user')
            ->allowedSorts([
                'updated_at'
            ])
            ->allowedFilters([
                AllowedFilter::scope('user', 'filter_by_user'),
            ]);
    }
}
