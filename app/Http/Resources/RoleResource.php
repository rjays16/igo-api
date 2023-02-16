<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
           'id'=>$this->id,
           'role'=>$this->role,
           'description'=>$this->description,
           'permission'=>$this->permission,
           'created_at'=>$this->created_at->toDateTimeString(),
           'value'=>$this->id,
           'label'=>$this->role,
        ];
    }
}
