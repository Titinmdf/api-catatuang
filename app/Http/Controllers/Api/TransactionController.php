<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['wallet', 'category'])
                           ->where('user_id', $request->user()->id);

        // Filter by date range
        if ($request->has('start_date')) {
            $query->where('transaction_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date')) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        // Filter by type
        if ($request->has('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }

        // Filter by wallet
        if ($request->has('wallet_id')) {
            $query->where('wallet_id', $request->wallet_id);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wallet_id' => 'required|exists:wallets,id',
            'category_id' => 'required|exists:user_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify wallet belongs to user
        $wallet = Wallet::where('id', $request->wallet_id)
                       ->where('user_id', $request->user()->id)
                       ->first();

        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet not found or access denied'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Create transaction
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'wallet_id' => $request->wallet_id,
                'category_id' => $request->category_id,
                'amount' => $request->amount,
                'description' => $request->description,
                'transaction_date' => $request->transaction_date,
                'type' => $request->type,
            ]);

            // Update wallet balance
            if ($request->type === 'income') {
                $wallet->increment('balance', $request->amount);
            } else {
                $wallet->decrement('balance', $request->amount);
            }

            $transaction->load(['wallet', 'category']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => $transaction
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create transaction'
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $transaction = Transaction::with(['wallet', 'category'])
                                ->where('user_id', $request->user()->id)
                                ->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::where('user_id', $request->user()->id)
                                ->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'wallet_id' => 'required|exists:wallets,id',
            'category_id' => 'required|exists:user_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify wallet belongs to user
        $wallet = Wallet::where('id', $request->wallet_id)
                       ->where('user_id', $request->user()->id)
                       ->first();

        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet not found or access denied'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Revert old transaction from wallet balance
            $oldWallet = Wallet::find($transaction->wallet_id);
            if ($transaction->type === 'income') {
                $oldWallet->decrement('balance', $transaction->amount);
            } else {
                $oldWallet->increment('balance', $transaction->amount);
            }

            // Update transaction
            $transaction->update([
                'wallet_id' => $request->wallet_id,
                'category_id' => $request->category_id,
                'amount' => $request->amount,
                'description' => $request->description,
                'transaction_date' => $request->transaction_date,
                'type' => $request->type,
            ]);

            // Apply new transaction to wallet balance
            if ($request->type === 'income') {
                $wallet->increment('balance', $request->amount);
            } else {
                $wallet->decrement('balance', $request->amount);
            }

            $transaction->load(['wallet', 'category']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction updated successfully',
                'data' => $transaction
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update transaction'
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $transaction = Transaction::where('user_id', $request->user()->id)
                                ->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Revert transaction from wallet balance
            $wallet = Wallet::find($transaction->wallet_id);
            if ($transaction->type === 'income') {
                $wallet->decrement('balance', $transaction->amount);
            } else {
                $wallet->increment('balance', $transaction->amount);
            }

            $transaction->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transaction'
            ], 500);
        }
    }

    public function summary(Request $request)
    {
        $userId = $request->user()->id;
        
        // Get date range (default to current month)
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $transactions = Transaction::where('user_id', $userId)
                                 ->whereBetween('transaction_date', [$startDate, $endDate]);

        $totalIncome = (clone $transactions)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $transactions)->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        // Get wallet balances
        $walletBalances = Wallet::where('user_id', $userId)
                               ->selectRaw('SUM(balance) as total_balance')
                               ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'summary' => [
                    'total_income' => $totalIncome,
                    'total_expense' => $totalExpense,
                    'balance' => $balance,
                    'wallet_balance' => $walletBalances->total_balance ?? 0,
                ]
            ]
        ]);
    }
}