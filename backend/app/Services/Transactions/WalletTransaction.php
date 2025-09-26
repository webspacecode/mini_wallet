<?php

namespace App\Services\Transactions;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Events\AmountTransferred;


class WalletTransaction implements TransactionStrategy
{

    public function authorize(Request $request): array
    {
        $validated = $request->validate([
            'receiver_id' => ['required','exists:users,id', function ($attribute, $value, $fail) use ($request) {
                if ($value == $request->user()->id) {
                    $fail('You cannot send money to yourself.');
                }
            }],
            'amount' => 'required|numeric|min:1'
        ]);

        // Check if sender has balance to transfer
        if ($request->user()->balance < $request->amount) {
            return [
                'success' => false,
                'message' => 'Insufficient balance',
                'code'    => 400,
            ];
        }

        // After cheking all possible negations return truth
        return [
            'success' => true,
            'message' => 'Authorized transaction',
            'code'    => 200,
        ];
    }

    public function capture(Request $request): JsonResponse
    {
        $maxRetries = 3;
        $attempt = 0;

        // Attempting for 3 MAX tries to provide high concurrency
        while ($attempt < $maxRetries) {
            $attempt++;

            try {
                // Here first start the db transaction 
                DB::beginTransaction();
                $amount = $request->amount;
                
                $sender = $request->user();
                $receiver = User::find($request->receiver_id);

                // Get the current update counter of sender and receiver  
                $senderCounter = $sender->update_counter;
                $receiverCounter = $receiver->update_counter;

                // Then Get the calculated commision fee
                $commisionFee = $this->calculateCommision($request->amount);

            
                // Then update the both sender and receiver balance 
                // based on current update counter. In order to handle 
                // the race condition and deadlock we are using optimistic locking

                //  ---- SENDER UPDATE -----
                $sender->balance = round($sender->balance - ($amount + $commisionFee), 2);
                $sender->update_counter += 1;

                $updateSender = User::where('id', $sender->id)
                ->where('update_counter', $senderCounter)
                ->update([
                    'balance' => $sender->balance,
                    'update_counter' => $sender->update_counter
                ]);

                if (!$updateSender) {
                    throw new \Exception("Conflict detected at your bank. Please retry.");
                }

                //  ---- RECEIVER UPDATE -----
                $receiver->balance = round($receiver->balance + $amount, 2);
                $receiver->update_counter += 1;

                $updateReceiver = User::where('id', $receiver->id)
                ->where('update_counter', $receiverCounter)
                ->update([
                    'balance' => $receiver->balance,
                    'update_counter' => $receiver->update_counter
                ]);

                if (!$updateReceiver) {
                    throw new \Exception("Conflict detected at receiver's bank. Please retry.");
                }

                // Then make the transaction log lastly
                $transaction = Transaction::create([
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'amount' => $request->amount,
                    'commission_fee' => $commisionFee
                ]);

                DB::commit();

                // Broadcast event to both sender and receiver
                event(new AmountTransferred($transaction));
                
                return response()->json([
                    'success' => true,
                    'sender_balance' =>  round($sender->balance, 2),
                    'receiver_balance' => round( $receiver->balance, 2),
                    'message' => 'Transaction completed successfully.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack(); 

                usleep(50_000);
                
                if ($attempt == $maxRetries) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 500);
                }
            }

        }  
        
        // Fallback response
        return response()->json([
            'success' => false,
            'message' => 'Transaction failed for unknown reason'
        ], 500);
    }

    public function status(Request $request): array
    {

    }

    public function getTransactions(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->input('per_page', 10);
        
        return response()->json([
            'success' => true,
            'currency' => 'USD',
            'currency_symobol' => '$',
            'user'=> $user,
            'user_balance' =>  round($user->balance, 2),
            'transaction_history' => $user->allTransactions()->paginate($perPage),
            'message' => 'Transaction fetched successfully.'
        ]); 
    }

    public function calculateCommision($amount)
    {
        if (empty($amount)) {
            return 0;
        }

        // Calculate the 1.5% commision on amount
        $commission = (1.5 / 100) * $amount;
        return round($commission, 2);
    }
}