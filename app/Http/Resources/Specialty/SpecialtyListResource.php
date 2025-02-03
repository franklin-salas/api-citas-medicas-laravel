<?php

namespace App\Http\Resources\Specialty;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialtyListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->resource->id,
            "name" => $this->resource->name,
            "status" => $this->resource->status=='ACTIVO'?'Activo': 'Inactivo' ,
            "created_at" => $this->resource->created_at->format("Y/m/d"),
           
        ];
    }
}
