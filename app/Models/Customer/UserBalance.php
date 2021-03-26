<?php

namespace App\Models\Customer;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBalance extends BaseModel
{
    use HasFactory;

    protected $table = 'usrBalances';
}
