<?php

namespace App\Http\Controllers\Api\V1\Customer\Repositories;

use App\Http\Controllers\Api\V1\NAB\Repositories\NabRepository;
use App\Models\Customer\UserBalance;

class CustomerBalanceRepository
{
    /**
     * Return user customer balance model
     *
     * @return UserBalance
     */
    public function model() {
        return UserBalance::class;
    }

    /**
     * Return model instance
     *
     * @return UserBalance
     */
    public function modelInstance() {
        return new UserBalance();
    }

    /**
     * Get total unit from balance
     *
     * @return float
     */
    public function getTotalUnit() {
        $result = 0;
        $nabRepository = new NabRepository;

        $balances = $this->model()::query()
            ->whereHas('customer')
            ->get();
        
        if($balances) {
            foreach($balances as $balance) {
                $result +=  $balance->unit;
            }
        }

        return $result;
    }

    /**
     * Update all customer balance with last nab
     * @param float $newNab
     * @return void
     */
    public function updateAllBalance($newNab) {
        $balances = $this->model()::query()
            ->whereHas('customer')
            ->get();

        if($balances && $newNab) {
            foreach($balances as $balance) {
                $balance->nab = $newNab;
                $balance->balance = round($balance->unit * $newNab, 2, PHP_ROUND_HALF_DOWN);
                $balance->save();
            }
        }
    }
}