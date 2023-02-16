<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompoundPeriodResource extends JsonResource
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
           'compound_period'=>$this->compound_period,
           'description'=>$this->description,
           'created_at'=>$this->created_at->toDateTimeString(),
           'value'=>$this->id,
           'label'=>$this->compound_period,
        ];
    }
}
