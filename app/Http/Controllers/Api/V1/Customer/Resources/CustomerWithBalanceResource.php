<?php

namespace App\Http\Controllers\Api\V1\Customer\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerWithBalanceResource extends JsonResource
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
            'username' => $this->username,
            'balance' => [
                'total_unit' => $this->balance ? $this->balance->unit : 0,
                'total_amount_rupiah' => $this->balance ? $this->balance->balance : 0,
                'nab' => $this->balance ? $this->balance->nab : 1,
            ],
            'created_at' => date('Y-m-d H:i:s', strtotime($this->created_at))
        ];
    }
}
