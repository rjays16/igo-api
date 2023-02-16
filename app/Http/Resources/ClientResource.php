<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
           'first_name'=>$this->first_name,
           'last_name'=>$this->last_name,
           'gender'=>$this->gender,
           'date_of_birth'=>$this->date_of_birth,
           'email'=>$this->email,
           'phone'=>$this->phone,
           'organization_id'=>$this->organization_id,
           'organization'=>$this->organization,
           'address1'=>$this->address1,
           'address2'=>$this->address2,
           'city_id'=>$this->city_id,
           'city'=>$this->city,
           'state'=>$this->state,
           'zip'=>$this->zip,
           'client_type_id'=>$this->client_type_id,
           'client_type'=>$this->client_type,
           'ca_date'=>$this->ca_date,
           'note'=>$this->note,
           'tag'=>$this->tag,
           'created_at'=>$this->created_at->toDateTimeString(),
           'value'=>$this->id,
           'label'=>$this->last_name.', '.$this->first_name,

        ];
    }
}
