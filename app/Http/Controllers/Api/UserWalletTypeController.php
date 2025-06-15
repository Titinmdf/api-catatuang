<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserWalletType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserWalletTypeController extends Controller
{
    public function index(Request $request)
    {
        $walletTypes = UserWalletType::where('user_id', $request->user()->id)
                                    ->orderBy('name')
                                    ->get();

        return response()->json([
            'success' => true,
            'data' => $walletTypes
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $walletType = UserWalletType::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'icon' => $request->icon,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Wallet type created successfully',
            'data' => $walletType
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $walletType = UserWalletType::where('user_id', $request->user()->id)
                                   ->find($id);

        if (!$walletType) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet type not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $walletType
        ]);
    }

    public function update(Request $request, $id)
    {
        $walletType = UserWalletType::where('user_id', $request->user()->id)
                                   ->find($id);

        if (!$walletType) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet type not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $walletType->update([
            'name' => $request->name,
            'icon' => $request->icon,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Wallet type updated successfully',
            'data' => $walletType
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $walletType = UserWalletType::where('user_id', $request->user()->id)
                                   ->find($id);

        if (!$walletType) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet type not found'
            ], 404);
        }

        $walletType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wallet type deleted successfully'
        ]);
    }
}