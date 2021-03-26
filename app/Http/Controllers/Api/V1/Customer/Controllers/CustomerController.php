<?php

namespace App\Http\Controllers\Api\V1\Customer\Controllers;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Controllers\Api\V1\Customer\Repositories\CustomerBalanceRepository;
use App\Http\Controllers\Api\V1\Customer\Repositories\CustomerRepository;
use App\Http\Controllers\Api\V1\Customer\Resources\CustomerResource;
use App\Http\Controllers\Api\V1\NAB\Repositories\NabRepository;
use App\Http\Controllers\Api\V1\Transaction\Repositories\HistoryTransactionRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerController extends BaseApiController
{
    protected $customerRepository;
    protected $customerBalanceRepository;
    protected $historyTransactionRepository;
    protected $nabRepository;

    /**
     * Constructor
     *
     * @param CustomerRepository $customerRepository
     * @param CustomerBalanceRepository $customerBalanceRepository
     * @param HistoryTransactionRepository $historyTransactionRepository
     * @param NabRepository $nabRepository
     * @return $this
     */
    public function __construct(
        CustomerRepository $customerRepository,
        CustomerBalanceRepository $customerBalanceRepository,
        HistoryTransactionRepository $historyTransactionRepository,
        NabRepository $nabRepository
    )
    {
        $this->customerRepository = $customerRepository;
        $this->customerBalanceRepository = $customerBalanceRepository;
        $this->historyTransactionRepository = $historyTransactionRepository;
        $this->nabRepository = $nabRepository;
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
                // NAB
                $nabCurrent = $this->nabRepository->getLastNabAmount(); 

                $customerBalance = $this->customerBalanceRepository->modelInstance();
                $customerBalance->nab = $nabCurrent; 
                $customerBalance->balance = 0;
                $customerBalance->unit = 0; 

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

     /**
     * Topup balance.
     *
     * @param  Request  $request
     * @return JsonResource
     */
    public function topup(Request $request)
    {
        request()->validate([
            'user_id' => ['required'],
            'amount_rupiah' => ['required', 'regex:/^\d*(\.\d{2})?$/']
        ]);

        $customer = $this->customerRepository->model()::query()
                ->with('balance')
                ->whereHas('balance')
                ->where('id', $request->get('user_id'))
                ->first();
                
        if(! $customer) {
            return $this->responseJson(new JsonResource(null), 404, 'Data not found');
        }

        // NAB
        $nabCurrent = $this->nabRepository->getLastNabAmount(); 

        // Topup
        $balanceTopup = (float) $request->get('amount_rupiah');
        $unitTopup = round($balanceTopup / $nabCurrent, 4, PHP_ROUND_HALF_DOWN);
        
        // Current
        $balanceCurrent = $customer->balance ? round($customer->balance->unit * $nabCurrent, 2, PHP_ROUND_HALF_DOWN) : 0;
        $unitCurrent = $customer->balance ? round($customer->balance->unit, 4, PHP_ROUND_HALF_DOWN) : 0;
        
        // Balance after
        $unitAfter = round($unitTopup + $unitCurrent, 4, PHP_ROUND_HALF_DOWN);
        $balanceAfter = round($unitAfter * $nabCurrent, 2, PHP_ROUND_HALF_DOWN);

        // Info for history
        $description = 'Topup Rp. ' . $balanceTopup;
        $type = 'topup';
                
        try {
            DB::beginTransaction();

            // Update balance customer
            $updateCustomerBalance = $this->customerBalanceRepository->model()::query()
                ->where('usrCustomer_id', $customer->id)
                ->update(array(
                    'nab' => $nabCurrent,
                    'balance' => $balanceAfter,
                    'unit' => $unitAfter
                ));

            // Create log history transaction
            $historyTransaction = $this->historyTransactionRepository->create(
                $customer,
                $nabCurrent,
                $balanceTopup,
                $balanceCurrent,
                $balanceAfter,
                $unitCurrent,
                $unitAfter,
                $description,
                $type
            );

            DB::commit();
        } catch(Exception $e) {
            DB::rollBack();
            return $this->responseJson(new JsonResource(null), 500, $e->getMessage());    
        }

        $responseJson = array(
            'id' => $customer->id,
            'name' => $customer->name,
            'username' => $customer->username,
            'nilai_unit_hasil_topup' => $unitTopup,
            'nilai_unit_total' => $unitAfter,
            'saldo_rupiah_total' => $balanceAfter
        );

        return $this->responseJsonFromArray($responseJson);
    }

    /**
     * Withdraw balance.
     *
     * @param  Request  $request
     * @return JsonResource
     */
    public function withdraw(Request $request)
    {
        request()->validate([
            'user_id' => ['required'],
            'amount_rupiah' => ['required', 'regex:/^\d*(\.\d{2})?$/']
        ]);

        $customer = $this->customerRepository->model()::query()
                ->with('balance')
                ->whereHas('balance')
                ->where('id', $request->get('user_id'))
                ->first();
                
        if(! $customer) {
            return $this->responseJson(new JsonResource(null), 404, 'Data not found');
        }

        // NAB
        $nabCurrent = $this->nabRepository->getLastNabAmount(); 

        // Topup
        $balanceWitdraw = (float) $request->get('amount_rupiah');
        $unitWithdraw = round($balanceWitdraw / $nabCurrent, 4, PHP_ROUND_HALF_DOWN);
        
        // Current
        $balanceCurrent = $customer->balance ? round($customer->balance->unit * $nabCurrent, 2, PHP_ROUND_HALF_DOWN) : 0;
        $unitCurrent = $customer->balance ? round($customer->balance->unit, 4, PHP_ROUND_HALF_DOWN) : 0;
        
        if($unitWithdraw <= $unitCurrent) {
             // Balance after
            $unitAfter = round($unitCurrent - $unitWithdraw, 4, PHP_ROUND_HALF_DOWN);
            $balanceAfter = round($unitAfter * $nabCurrent, 2, PHP_ROUND_HALF_DOWN);

            // Info for history
            $description = 'Withdraw Rp. ' . $balanceWitdraw;
            $type = 'withdraw';
                    
            try {
                DB::beginTransaction();

                // Update balance customer
                $updateCustomerBalance = $this->customerBalanceRepository->model()::query()
                    ->where('usrCustomer_id', $customer->id)
                    ->update(array(
                        'nab' => $nabCurrent,
                        'balance' => $balanceAfter,
                        'unit' => $unitAfter
                    ));

                // Create log history transaction
                $historyTransaction = $this->historyTransactionRepository->create(
                    $customer,
                    $nabCurrent,
                    $balanceWitdraw,
                    $balanceCurrent,
                    $balanceAfter,
                    $unitCurrent,
                    $unitAfter,
                    $description,
                    $type
                );

                DB::commit();
            } catch(Exception $e) {
                DB::rollBack();
                return $this->responseJson(new JsonResource(null), 500, $e->getMessage());    
            }

            $responseJson = array(
                'id' => $customer->id,
                'name' => $customer->name,
                'username' => $customer->username,
                'nilai_unit_hasil_withdraw' => $unitWithdraw,
                'nilai_unit_total' => $unitAfter,
                'saldo_rupiah_total' => $balanceAfter
            );

            return $this->responseJsonFromArray($responseJson);
        } else {
            return $this->responseJson(new JsonResource(null), 400, 'Cannot withdraw, unit withdraw exceed unit asset');
        }
    }
}
