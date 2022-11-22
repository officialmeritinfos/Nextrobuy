<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_id',
        'account_name',
        'account_number',
        'bvn_number'
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
