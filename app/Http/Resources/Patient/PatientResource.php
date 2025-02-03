<?php

namespace App\Http\Resources\Patient;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
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
            "document" => $this->resource->document? $this->resource->document: '' ,
            "email" => $this->resource->email?$this->resource->email: '',
            "mobile" => $this->resource->mobile,
            "birth_date" => $this->resource->birth_date ? Carbon::parse($this->resource->birth_date)->format("d/m/Y") : NULL,
            "gender" => $this->resource->gender,
            "antecedent_family" => $this->resource->antecedent_family,
            "antecedent_allergy" => $this->resource->antecedent_allergy,
            "address" => $this->resource->address,        
            "url_avatar" =>  $this->resource->avatar? env("APP_URL")."/storage/".$this->resource->avatar: env("APP_URL")."/storage/img/user.jpg",
        ];
    }
}
