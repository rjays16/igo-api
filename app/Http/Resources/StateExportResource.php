<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StateExportResource extends JsonResource
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
            'state'=>$this->state,
           'name'=>$this->name,
           'description'=>$this->description,
           'created_at'=>$this->created_at->toDateTimeString(),
         ];
    }
}
