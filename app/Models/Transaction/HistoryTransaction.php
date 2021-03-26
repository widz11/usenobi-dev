<?php

namespace App\Models\Transaction;

use App\Models\BaseModel;
use App\Models\NAB\NAB;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistoryTransaction extends BaseModel
{
    use HasFactory;

    protected $table = 'hisTransactions';

    /**
     * Relation customer with history transaction
     *
     * @return $this
     */
    public function nab() {
        return $this->belongsTo(NAB::class, 'nab_id');
    }
}
