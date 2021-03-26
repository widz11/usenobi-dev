<?php

namespace App\Http\Controllers\Api\V1\NAB\Repositories;

use App\Models\NAB\NAB;

class NabRepository
{
    /**
     * Return model
     *
     * @return NAB
     */
    public function model() {
        return NAB::class;
    }

    /**
     * Return model instance
     *
     * @return NAB
     */
    public function modelInstance() {
        return new NAB();
    }

    /**
     * Get last nab amount
     *
     * @return float
     */
    public function getLastNabAmount() {
        $nab = $this->model()::query()
            ->orderBy('date', 'desc')
            ->first();

        $result = $nab ? $nab->amount : 1;

        return $result;
    }
}