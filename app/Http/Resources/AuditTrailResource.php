<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuditTrailResource extends JsonResource
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
           'user_id'=>$this->user_id,
           'pages'=>$this->pages,
           'activity'=>$this->activity,
           'created_at'=>$this->created_at->toDateTimeString(),
          // 'value'=>$this->pages,
          // 'label'=>$this->activity,
        ];
    }
}
