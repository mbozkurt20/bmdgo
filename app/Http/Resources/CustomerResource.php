<?php

namespace App\Http\Resources;

use App\Models\CustomerAddress;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{

    public function toArray($request)
    {
        $courierAddress = CustomerAddress::where('customer_id',$this->id)->first();
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'lat' => $courierAddress?->latitude,
            'long' => $courierAddress?->longitude,
            'quarter' => $courierAddress?->mahalle,
            'street' => $courierAddress?->sokak_cadde,
            'floor' => $courierAddress?->daire_no,
            'flat_number' => $courierAddress?->daire_no,
            'address_directions' => $courierAddress?->adres_tarifi,
        ];
    }
}
