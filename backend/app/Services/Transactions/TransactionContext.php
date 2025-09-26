<?php

namespace App\Services\Transactions;

use Illuminate\Http\Request;

class TransactionContext 
{
    private TransactionStrategy $strategy;

    public function __construct(TransactionStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function strategy(): TransactionStrategy
    {
        return $this->strategy;
    }
}