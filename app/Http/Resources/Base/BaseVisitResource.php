<?php

namespace App\Http\Resources\Base;

use App\Http\Resources\Base\BaseVisitIndexResource;

class BaseVisitResource extends BaseVisitIndexResource
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


