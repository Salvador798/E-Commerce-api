<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'carrier' => $this->carrier,
            'tracking' => $this->tracking,
            'estimated_date' => $this->estimated_date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'order' => $this->whenLoaded('order', function () {
                return [
                    'id' => $this->order->id,
                    'status' => $this->order->status,
                    'total' => $this->order->total,
                    'address_id' => $this->order->address_id,
                ];
            }),
        ];
    }
}
