<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserCategory;
use App\Models\UserWalletType;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo user
        $user = User::create([
            'name' => 'Demo User',
            'email' => 'demo@catatuang.com',
            'password' => Hash::make('password123'),
        ]);

        // Create default categories
        $categories = [
            // Income categories
            ['name' => 'Gaji', 'type' => 'income', 'icon' => 'salary'],
            ['name' => 'Freelance', 'type' => 'income', 'icon' => 'freelance'],
            ['name' => 'Investasi', 'type' => 'income', 'icon' => 'investment'],
            ['name' => 'Bonus', 'type' => 'income', 'icon' => 'bonus'],
            
            // Expense categories
            ['name' => 'Makanan', 'type' => 'expense', 'icon' => 'food'],
            ['name' => 'Transportasi', 'type' => 'expense', 'icon' => 'transport'],
            ['name' => 'Belanja', 'type' => 'expense', 'icon' => 'shopping'],
            ['name' => 'Tagihan', 'type' => 'expense', 'icon' => 'bills'],
            ['name' => 'Hiburan', 'type' => 'expense', 'icon' => 'entertainment'],
            ['name' => 'Kesehatan', 'type' => 'expense', 'icon' => 'health'],
        ];

        foreach ($categories as $category) {
            UserCategory::create([
                'user_id' => $user->id,
                'name' => $category['name'],
                'type' => $category['type'],
                'icon' => $category['icon'],
            ]);
        }

        // Create default wallet types
        $walletTypes = [
            ['name' => 'Bank', 'icon' => 'bank'],
            ['name' => 'E-Wallet', 'icon' => 'ewallet'],
            ['name' => 'Tunai', 'icon' => 'cash'],
            ['name' => 'Kartu Kredit', 'icon' => 'credit_card'],
        ];

        foreach ($walletTypes as $walletType) {
            UserWalletType::create([
                'user_id' => $user->id,
                'name' => $walletType['name'],
                'icon' => $walletType['icon'],
            ]);
        }

        // Create demo wallets
        $bankWalletType = UserWalletType::where('user_id', $user->id)->where('name', 'Bank')->first();
        $ewalletType = UserWalletType::where('user_id', $user->id)->where('name', 'E-Wallet')->first();
        $cashType = UserWalletType::where('user_id', $user->id)->where('name', 'Tunai')->first();

        $wallets = [
            [
                'name' => 'BCA Tabungan',
                'user_wallet_type_id' => $bankWalletType->id,
                'balance' => 5000000,
            ],
            [
                'name' => 'GoPay',
                'user_wallet_type_id' => $ewalletType->id,
                'balance' => 250000,
            ],
            [
                'name' => 'Dompet Tunai',
                'user_wallet_type_id' => $cashType->id,
                'balance' => 150000,
            ],
        ];

        foreach ($wallets as $wallet) {
            Wallet::create([
                'user_id' => $user->id,
                'user_wallet_type_id' => $wallet['user_wallet_type_id'],
                'name' => $wallet['name'],
                'balance' => $wallet['balance'],
            ]);
        }

        // Create demo transactions
        $walletBCA = Wallet::where('user_id', $user->id)->where('name', 'BCA Tabungan')->first();
        $walletGoPay = Wallet::where('user_id', $user->id)->where('name', 'GoPay')->first();
        $walletCash = Wallet::where('user_id', $user->id)->where('name', 'Dompet Tunai')->first();

        $salaryCategory = UserCategory::where('user_id', $user->id)->where('name', 'Gaji')->first();
        $foodCategory = UserCategory::where('user_id', $user->id)->where('name', 'Makanan')->first();
        $transportCategory = UserCategory::where('user_id', $user->id)->where('name', 'Transportasi')->first();

        $transactions = [
            [
                'wallet_id' => $walletBCA->id,
                'category_id' => $salaryCategory->id,
                'amount' => 5000000,
                'description' => 'Gaji bulan ini',
                'transaction_date' => now()->format('Y-m-d'),
                'type' => 'income',
            ],
            [
                'wallet_id' => $walletGoPay->id,
                'category_id' => $foodCategory->id,
                'amount' => 25000,
                'description' => 'Makan siang',
                'transaction_date' => now()->format('Y-m-d'),
                'type' => 'expense',
            ],
            [
                'wallet_id' => $walletCash->id,
                'category_id' => $transportCategory->id,
                'amount' => 15000,
                'description' => 'Ongkos angkot',
                'transaction_date' => now()->subDay()->format('Y-m-d'),
                'type' => 'expense',
            ],
        ];

        foreach ($transactions as $transaction) {
            Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $transaction['wallet_id'],
                'category_id' => $transaction['category_id'],
                'amount' => $transaction['amount'],
                'description' => $transaction['description'],
                'transaction_date' => $transaction['transaction_date'],
                'type' => $transaction['type'],
            ]);
        }
    }
}
