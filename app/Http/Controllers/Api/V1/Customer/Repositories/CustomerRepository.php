<?php

namespace App\Http\Controllers\Api\V1\Customer\Repositories;

use App\Models\Customer\UserCustomer;

class CustomerRepository
{
    /**
     * Return user customer model
     *
     * @return UserCustomer
     */
    public function model() {
        return UserCustomer::class;
    }

    /**
     * Return model instance
     *
     * @return NAB
     */
    public function modelInstance() {
        return new UserCustomer();
    }
}