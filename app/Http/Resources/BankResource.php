<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->bank) {
            $bankName = $this->bank->bank_name;
            $abbreviation = $this->bank->abbreviation;
            $bankCode = $this->bank->bank_code;
        } else {
            $bankName = NULL;
            $abbreviation = NULL;
            $bankCode = NULL;
        }
        return [
            'accountName' => $this->account_name,
            'accountNumber' => $this->account_number,
            'bvnNumber' => $this->bvn_number,
            'bankName' => $bankName,
            'abbreviation' => $abbreviation,
            'bankCode' => $bankCode
        ];
    }
}
