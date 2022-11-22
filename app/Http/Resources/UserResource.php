<?php

namespace App\Http\Resources;

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
        if ($this->image) {
            $image = url('/storage/' . $this->image);
        } else {
            $image = NULL;
        }

        return [
            'userID' => $this->id,
            'email' => $this->email,
            'firstName' => $this->firstname,
            'lastName' => $this->lastname,
            'fullName' => $this->full_name,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'emailVerifiedStatus' => $this->is_email_verified,
            'role' => $this->role,
            'dateOfBirth' => $this->date_of_birth,
            'isActive' => $this->is_active,
            'dateJoined' => $this->created_at,
            'applicationName' => $this->application_name,
            'image' => $image,
            'bank' => new BankResource($this->bank),
            'wallet' => new WalletResource($this->wallet)
        ];
    }
}
