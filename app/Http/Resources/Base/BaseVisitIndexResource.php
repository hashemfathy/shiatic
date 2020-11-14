<?php

namespace App\Http\Resources\Base;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseVisitIndexResource extends JsonResource
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
            'client_name' => $this->client_name,
            'complaint' => $this->complaint,
            'price' => $this->price,
            'date' => $this->date,
            'hour' => $this->hour,
            'duration' => $this->duration,
            'client_id' => $this->client_id,
            'new_client' => $this->client->new_client,
            'specialist' => $this->specialist,
        ];
    }
}
