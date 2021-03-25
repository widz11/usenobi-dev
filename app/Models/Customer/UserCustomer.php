<?php

namespace App\Models\Customer;

use App\Models\Transaction\HistoryTransaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCustomer extends Model
{
    use HasFactory;

    protected $table = 'usrCustomers';

    /**
     * Relation customer with balance
     *
     * @return $this
     */
    public function balance() {
        return $this->hasOne(UserBalance::class, 'usrCutomer_id');
    }

    /**
     * Relation customer with history transaction
     *
     * @return $this
     */
    public function historyTransaction() {
        return $this->hasMany(HistoryTransaction::class, 'usrCutomer_id');
    }
}
