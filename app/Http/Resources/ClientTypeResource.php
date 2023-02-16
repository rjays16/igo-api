<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\HttpStatusCode;
use App\Enums\SystemMessage;

class ClientTypeResource extends JsonResource
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
           'client_type'=>$this->client_type,
           'description'=>$this->description,
           'created_at'=>$this->created_at->toDateTimeString(),
           'value'=>$this->id,
           'label'=>$this->client_type,
        ];
    }
}
