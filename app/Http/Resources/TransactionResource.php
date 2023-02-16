<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
        return[
            'id'=>$this->id,
            'account_id'=>$this->account_id,
            'acct_description'=>$this->acct_description,
            'effective_date'=>$this->effective_date,
            'trans_type_id'=>$this->trans_type_id,
            'trans_type'=>$this->trans_type,
            'memo'=>$this->memo,
            'amount'=>$this->amount,
            'entry_date'=>$this->entry_date,
            'created_at'=>$this->created_at->toDateTimeString(),
         ];
    }
}
