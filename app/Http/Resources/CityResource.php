<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
           'state'=>$this->state,
           'city'=>$this->city,
           'description'=>$this->description,
           'created_at'=>$this->created_at->toDateTimeString(),
           'value'=>$this->id,
           'label'=>$this->city,
        ];
    }
}
