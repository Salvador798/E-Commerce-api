<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckoutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_id' => $this['order']->id ?? null,
            'total' => $this['order']->total ?? null,
            'status' => $this['order']->status ?? null,

            'items' => $this['items'] ?? [],

            'payment_method' => $this['payment_method'] ?? null,
            'address_id' => $this['address_id'] ?? null,
        ];
    }
}
