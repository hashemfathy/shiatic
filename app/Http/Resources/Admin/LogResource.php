<?php

namespace App\Http\Resources\Admin;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class LogResource extends JsonResource
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
            'action' => $this->description,
            'user' => $this->user->name,
            'created_at' => $this->created_at->format("Y-m-d H:i:s"),
            'updated_at' => $this->updated_at->format("Y-m-d H:i:s"),
            'model' => $this->loggable_type::find($this->loggable_id),
        ];
    }
}
