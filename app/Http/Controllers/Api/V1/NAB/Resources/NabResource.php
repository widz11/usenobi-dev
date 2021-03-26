<?php

namespace App\Http\Controllers\Api\V1\NAB\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NabResource extends JsonResource
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
            'amount' => $this->amount,
            'date' => date('Y-m-d', strtotime($this->date)),
            'created_at' => date('Y-m-d H:i:s', strtotime($this->created_at))
        ];
    }
}
