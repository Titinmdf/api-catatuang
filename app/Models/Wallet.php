<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_wallet_type_id',
        'name',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userWalletType()
    {
        return $this->belongsTo(UserWalletType::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}