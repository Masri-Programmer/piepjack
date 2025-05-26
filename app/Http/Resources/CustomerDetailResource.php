<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'address_line_one' => $this->address_line_one,
            'address_line_two' => $this->address_line_two,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
        ];
    }
}
