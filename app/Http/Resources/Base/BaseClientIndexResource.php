<?php

namespace App\Http\Resources\Base;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseClientIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'code' => $this->code,
            'blood' => $this->blood,
            'age' => $this->age,
            'weight' => $this->weight,
            'visits' => $this->visits,

        ];
    }
}
