<?php

namespace App\Http\Resources\Patient;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientListResource extends JsonResource
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
            "last_name" => $this->resource->last_name,
            "email" => $this->resource->email,
            "document" => $this->resource->document? $this->resource->document: '' ,
            "mobile" => $this->resource->mobile,
            "created_at" => $this->resource->created_at->format("Y/m/d"),
            "avatar" =>  $this->resource->avatar? env("APP_URL")."/storage/".$this->resource->avatar: env("APP_URL")."/storage/img/user.jpg",
        ];
    }
}
