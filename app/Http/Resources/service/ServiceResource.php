<?php

namespace App\Http\Resources\service;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        'id' => $this->resource->id, 
        'name' => $this->resource->name, 
        'description' => $this->resource->description,
        'specialty_id' => $this->resource->specialty_id,
        'price' => $this->resource->price,
        'status' => $this->resource->status,
        'created_at' => $this->resource->created_at->format('Y/m/d'),


        ];
    }
}
