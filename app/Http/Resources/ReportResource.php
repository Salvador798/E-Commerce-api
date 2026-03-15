<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'label' => $this->name ?? $this->day ?? $this->method ?? $this->status ?? $this->city ?? null,
            'value' => $this->total ?? $this->total_generated ?? $this->total_spent ?? $this->quantity ?? $this->units_sold ?? 0,
            'extra' => [
                'units_sold' => $this->units_sold ?? null,
                'total_generated' => $this->total_generated ?? null,
                'total_spent' => $this->total_spent ?? null,
                'quantity' => $this->quantity ?? null,
            ]
        ];
    }
}
