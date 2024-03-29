<?php

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'food_id', 'user_id', 'quantity', 'total', 'status', 'payment_url'
    ];

    //ini buat relasinya (liat di ERD FOOD MARKET)

    public function food()
    {
        return $this->hasOne(Food::class, 'id', 'food_id');
    }
    

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }



    public function getCreatedAtAttribure($value)
    {
        return Carbon::parse($value)->timestamp;
    }
    public function getUpdatedAttribure($value)
    {
        return Carbon::parse($value)->timestamp;
    }
}
