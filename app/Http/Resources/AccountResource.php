<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'status_id'=>$this->status_id,
            'status'=>$this->status,
            'creditor_id'=>$this->creditor_id,
            'creditor_name'=>$this->last_name.", ".$this->first_name,
            'acct_description'=>$this->acct_description,
            'acct_number'=>$this->acct_number,
            'debtor_id'=>$this->debtor_id,
            'term_id'=>$this->term_id,
            'rate'=>$this->current_rate,
            'note'=>$this->note,
            'origin_date'=>$this->origin_date,
            'tag'=>$this->tag,
            'created_at'=>$this->created_at->toDateTimeString(),
            'value'=>$this->id,
            'label'=>$this->acct_description,
        ];
    }
}
