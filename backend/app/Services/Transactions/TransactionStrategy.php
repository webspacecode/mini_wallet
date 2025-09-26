<?php

namespace App\Services\Transactions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


interface TransactionStrategy
{

    public function getTransactions(Request $request): JsonResponse;

    public function authorize(Request $request): array;

    public function capture(Request $request): JsonResponse;

    public function status(Request $request): array;

}