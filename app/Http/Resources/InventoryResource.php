<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
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
            'product_id' => $this->product_id,
            'available_quantity' => $this->available_quantity,

            'product' => [
                'id' => $this->product?->id,
                'name' => $this->product?->name,
                'price' => $this->product?->price,
            ],

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
