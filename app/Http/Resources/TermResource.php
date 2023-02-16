<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TermResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
       //return parent::toArray($request);
       return [
        'id'=>$this->id,
        'account_id'=>$this->account_id,
        'acct_description'=>$this->acct_description,
        'effective_date'=>$this->effective_date,
        'rate'=>$this->rate,
        'compound_period_id'=>$this->compound_period_id,
        'compound_period'=>$this->compound_period,
        'note'=>$this->note,
        'created_at'=>$this->created_at->toDateTimeString(),
    ];
    }
}
