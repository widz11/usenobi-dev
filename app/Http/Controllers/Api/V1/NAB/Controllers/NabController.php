<?php

namespace App\Http\Controllers\Api\V1\NAB\Controllers;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Controllers\Api\V1\Customer\Repositories\CustomerBalanceRepository;
use App\Http\Controllers\Api\V1\NAB\Repositories\NabRepository;
use App\Http\Controllers\Api\V1\NAB\Resources\NabResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class NabController extends BaseApiController
{
    protected $nabRepository;
    protected $customerBalanceRepository;

    /**
     * Constructor
     *
     * @param NabRepository $nabRepository
     * @return $this
     */
    public function __construct(
        NabRepository $nabRepository,
        CustomerBalanceRepository  $customerBalanceRepository
    )
    {
        $this->nabRepository = $nabRepository;
        $this->customerBalanceRepository = $customerBalanceRepository;
    }

    /**
     * Update balance NAB.
     *
     * @param  Request  $request
     * @return JsonResource
     */
    public function updateBalance(Request $request)
    {
        request()->validate([
            'current_balance' => ['required', 'regex:/^\d*(\.\d{2})?$/']
        ]);

        try {
            // Generate new NAB
            DB::beginTransaction();
            
            $currentBalance = (float) $request->get('current_balance');
            $currentTotalUnit = $this->customerBalanceRepository->getTotalUnit();
            $newNab = 1;
            if($currentTotalUnit > 0) {
                $newNab = round($currentBalance / $currentTotalUnit, 4, PHP_ROUND_HALF_DOWN); 
            }
            $nab = $this->nabRepository->model()::query()
                ->create(array(
                    'amount' => $newNab,
                    'date' => date('Y-m-d H:i:s')
                ));

            // Update balance
            $this->customerBalanceRepository->updateAllBalance($newNab);

            DB::commit();
        } catch(Exception $e) {
            DB::rollBack();
            return $this->responseJson(new JsonResource(null), 500, $e->getMessage());    
        }

        return $this->responseJson(new NabResource($nab));
    }

    /**
     * Get nab list
     *
     * @return void
     */
    public function list() {
        $isPaginate = request('is_paginate') == 'true' ? true : false;
        $pageSize = request('page_size') ? (int) request('page_size') : 20;
        $currentPage = request('current_page')  ? (int) request('current_page') : 1;

        $nabList = $this->nabRepository->model()::query()
            ->latest();

        if($isPaginate) {
            $nabList = $nabList->paginate($pageSize, ['*'], 'page', $currentPage);
        } else {
            $nabList = $nabList->get();
        }

        return $this->responseJson(NabResource::collection($nabList));
    }
}
