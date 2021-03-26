<?php

namespace App\Models\Customer;

use App\Models\BaseModel;
use App\Models\Transaction\HistoryTransaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserCustomer extends BaseModel
{
    use HasFactory;

    protected $table = 'usrCustomers';

    /**
     * Relation customer with balance
     *
     * @return $this
     */
    public function balance() {
        return $this->hasOne(UserBalance::class, 'usrCustomer_id');
    }

    /**
     * Relation customer with history transaction
     *
     * @return $this
     */
    public function historyTransaction() {
        return $this->hasMany(HistoryTransaction::class, 'usrCustomer_id');
    }
}
