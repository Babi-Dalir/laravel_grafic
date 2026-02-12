<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;

class UserTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'money',
        'type',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public static function soldProductBySeller($order_details)
    {
        foreach ($order_details as $order_detail){
            UserTransaction::query()->create([
                'user_id'=>$order_detail->seller_id,
                'order_id'=>$order_detail->order_id,
                'money'=>OrderDetail::calculateMoneyForCommission($order_detail),
                'type'=>TransactionType::Deposit->value,
                'description'=>"وایز وجه به منظور فروش محصول",
            ]);
        }
    }
}
