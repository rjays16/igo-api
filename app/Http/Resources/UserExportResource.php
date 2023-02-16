<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserExportResource extends JsonResource
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
        'city'=>$this->city,
        'state'=>$this->state,
        'zip'=>$this->zip,
        'role'=>$this->role,
        'created_at'=>$this->created_at->toDateTimeString(),
     ];
    }
}
