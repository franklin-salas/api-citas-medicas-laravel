<?php

namespace App\Http\Resources\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "email" => $this->resource->email,
            "birth_date" => $this->resource->birth_date ? Carbon::parse($this->resource->birth_date)->format("d/m/Y") : NULL,
            "gender" => $this->resource->gender,
            "education" => $this->resource->education,
            "designation" => $this->resource->designation,
            "address" => $this->resource->address,
            "mobile" => $this->resource->mobile,
            "created_at" => $this->resource->created_at->format("Y/m/d"),
            "role_id" => $this->resource->roles->first()->id,
            // "avatar" =>  $this->resource->avatar,
            "url_avatar" =>  $this->resource->avatar? env("APP_URL")."/storage/".$this->resource->avatar: env("APP_URL")."/storage/img/user.jpg",
        ];
    }
}
