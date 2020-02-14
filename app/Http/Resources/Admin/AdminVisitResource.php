<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Base\BaseVisitResource;

class AdminVisitResource extends BaseVisitResource
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


