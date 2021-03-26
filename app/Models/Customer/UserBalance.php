<?php

namespace App\Models\Customer;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBalance extends BaseModel
{
    use HasFactory;

    protected $table = 'usrBalances';

    /**
     * Relation customer
     *
     * @return $this
     */
    public function customer() {
        return $this->belongsTo(UserCustomer::class, 'usrCustomer_id');
    }
}
