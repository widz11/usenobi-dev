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
     * @return NAB
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
        $lastNab = $nabRepository->getLastNabAmount();

        $balances = $this->model()::query()
            ->whereHas('customer')
            ->get();
        
        if($balances) {
            foreach($balances as $balance) {
                $result +=  round($balance->balance / $lastNab, 5, PHP_ROUND_HALF_DOWN);
            }
        }

        return $result;
    }
}