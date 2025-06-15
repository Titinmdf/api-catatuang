<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $wallets = Wallet::with('userWalletType')
                        ->where('user_id', $request->user()->id)
                        ->orderBy('name')
                        ->get();

        return response()->json([
            'success' => true,
            'data' => $wallets
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'user_wallet_type_id' => 'nullable|exists:user_wallet_types,id',
            'balance' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $wallet = Wallet::create([
            'user_id' => $request->user()->id,
            'user_wallet_type_id' => $request->user_wallet_type_id,
            'name' => $request->name,
            'balance' => $request->balance ?? 0,
        ]);

        $wallet->load('userWalletType');

        return response()->json([
            'success' => true,
            'message' => 'Wallet created successfully',
            'data' => $wallet
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $wallet = Wallet::with('userWalletType')
                       ->where('user_id', $request->user()->id)
                       ->find($id);

        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $wallet
        ]);
    }

    public function update(Request $request, $id)
    {
        $wallet = Wallet::where('user_id', $request->user()->id)
                       ->find($id);

        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'user_wallet_type_id' => 'nullable|exists:user_wallet_types,id',
            'balance' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $wallet->update([
            'user_wallet_type_id' => $request->user_wallet_type_id,
            'name' => $request->name,
            'balance' => $request->balance ?? $wallet->balance,
        ]);

        $wallet->load('userWalletType');

        return response()->json([
            'success' => true,
            'message' => 'Wallet updated successfully',
            'data' => $wallet
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $wallet = Wallet::where('user_id', $request->user()->id)
                       ->find($id);

        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet not found'
            ], 404);
        }

        $wallet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wallet deleted successfully'
        ]);
    }
}