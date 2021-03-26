<?php

namespace App\Http\Controllers\Api\V1\Customer\Repositories;

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

    public function modelInstance() {
        return new UserBalance();
    }
}