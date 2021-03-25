<?php

namespace App\Models\Transaction;

use App\Models\NAB\NAB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryTransaction extends Model
{
    use HasFactory;

    protected $table = 'hisTransaction';

    /**
     * Relation customer with history transaction
     *
     * @return $this
     */
    public function nab() {
        return $this->belongsTo(NAB::class, 'nab_id');
    }
}
