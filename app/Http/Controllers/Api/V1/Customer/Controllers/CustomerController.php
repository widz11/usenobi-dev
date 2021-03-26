<?php

namespace App\Http\Controllers\Api\V1\Customer\Controllers;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Controllers\Api\V1\Customer\Repositories\CustomerBalanceRepository;
use App\Http\Controllers\Api\V1\Customer\Repositories\CustomerRepository;
use App\Http\Controllers\Api\V1\Customer\Resources\CustomerResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerController extends BaseApiController
{
    protected $customerRepository;
    protected $customerBalanceRepository;

    /**
     * Constructor
     *
     * @param CustomerRepository $customerRepository
     * @param CustomerBalanceRepository $customerBalanceRepository
     * @return $this
     */
    public function __construct(
        CustomerRepository $customerRepository,
        CustomerBalanceRepository $customerBalanceRepository
    )
    {
        $this->customerRepository = $customerRepository;
        $this->customerBalanceRepository = $customerBalanceRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResource
     */
    public function store(Request $request)
    {
        $customerTable = $this->customerRepository->modelInstance()->getTable();

        request()->validate([
            'name' => ['required'],
            'username' => ['required', Rule::unique($customerTable, 'username')]
        ]);

        try {
            DB::beginTransaction();
            // Create user customer
            $customer = $this->customerRepository->model()::query()
                ->create(array(
                    'name' => $request->get('name'),
                    'username' => $request->get('username')
                ));
            
            if($customer) {
                $customerBalance = $this->customerBalanceRepository->modelInstance();
                $customerBalance->balance = 0; 

                // Create user customer with balance relation
                $customer->balance()->save($customerBalance);
            }
            DB::commit();
        } catch(Exception $e) {
            DB::rollBack();
            return $this->responseJson(new JsonResource(null), 500, $e->getMessage());    
        }

        return $this->responseJson(new CustomerResource($customer));
    }
}
