<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
       // return parent::toArray($request);
       return [
        'id'=>$this->id,
        'status'=>$this->status,
        'description'=>$this->description,
        'created_at'=>$this->created_at->toDateTimeString(),
        //'deleted_at'=>$this->deleted_at->toDateTimeString(),
        'value'=>$this->id,
        'label'=>$this->status,
    ];
    }
}
