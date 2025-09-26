<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Transactions\WalletTransaction;
use App\Services\Transactions\TransactionContext;


class TransactionController extends Controller
{
    public function __construct() 
    {
        // Here you can change the strategy based 
        // on any condition or business logic 
        $this->strategy = new WalletTransaction();
        $this->context = new TransactionContext($this->strategy);
    }

    /**
     * Method to transfer the amount to other user
     */
    public function makeTransaction(Request $request) 
    {
        try {
            // Make a data validation here 
            $validation = $this->context->strategy()->authorize($request);

            if (!$validation['success']) {
                return $validation;
            }

            // Now do the capture transaction here 
            return $captureAndMakeTansaction = $this->context->strategy()->capture($request);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Transaction failed for unknown reason'
            ], 500);
        }   
    }

    /**
     * Method to get the transactions of current log in user
     */
    public function getTransactions(Request $request)
    {
        try {
            return $this->context->strategy()->getTransactions($request);
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Faild to fetch transactions for unknown reason'
            ], 500);
        }
    }
}
