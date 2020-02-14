<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Base\BaseVisitIndexResource;

class AdminVisitIndexResource extends BaseVisitIndexResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), []);
    }
}


