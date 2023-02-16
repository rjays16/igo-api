<?php

namespace App\Http\Resources;
use App\Enums\FileName;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
       return[
        'id'=>$this->id,
        'first_name'=>$this->first_name,
        'last_name'=>$this->last_name,
        'email'=>$this->email,
        'phone'=>$this->phone,
        'address1'=>$this->address1,
        'address2'=>$this->address2,
        'city_id'=>$this->city_id,
        'city'=>$this->city,
        'state'=>$this->state,
        'zip'=>$this->zip,
        'role_id'=>$this->role_id,
        'role'=>$this->role,
        'picture'=>url('/').FileName::ProfilePicPath."/".$this->picture,
        'created_at'=>$this->created_at->toDateTimeString(),
        'value'=>$this->id,
        'label'=>$this->first_name.' '.$this->last_name,
     ];
    }
}
