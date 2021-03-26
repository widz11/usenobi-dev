<?php

namespace App\Http\Controllers\Api\V1\Transaction\Repositories;

use App\Models\Customer\UserCustomer;
use App\Models\Transaction\HistoryTransaction;

class HistoryTransactionRepository
{
    /**
     * Return user customer model
     *
     * @return HistoryTransaction
     */
    public function model() {
        return HistoryTransaction::class;
    }

    /**
     * Return model instance
     *
     * @return HistoryTransacton
     */
    public function modelInstance() {
        return new HistoryTransaction();
    }

    /**
     * Create
     *
     * @param UserCustomer $userCustomer
     * @param float $nab
     * @param float $balanceInOut
     * @param float $balanceBefore
     * @param float $balanceAfter
     * @param float $unitBefore
     * @param float $unitAfter
     * @param string $description
     * @param string $type
     * @return HistoryTransaction
     */
    public function create(
        UserCustomer $userCustomer,
        float $nab = 1,
        float $balanceInOut = 0,
        float $balanceBefore = 0,
        float $balanceAfter = 0,
        float $unitBefore = 0,
        float $unitAfter = 0,
        $description = '',
        $type = ''
    ) {
        $history = $this->model()::query()
            ->create(array(
                'usrCustomer_id' => $userCustomer->id,
                'nab' => $nab,
                'balanceInOut' => $balanceInOut,
                'balanceBefore' => $balanceBefore,
                'balanceAfter' => $balanceAfter,
                'unitBefore' => $unitBefore,
                'unitAfter' => $unitAfter,
                'description' => $description,
                'type' => $type
            ));
        
        return $history;
    }
}