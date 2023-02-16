<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionTypeResource extends JsonResource
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
           'trans_type'=>$this->trans_type,
           'description'=>$this->description,
           'created_at'=>$this->created_at->toDateTimeString(),
           'value'=>$this->id,
           'label'=>$this->trans_type,
        ];
    }
}
