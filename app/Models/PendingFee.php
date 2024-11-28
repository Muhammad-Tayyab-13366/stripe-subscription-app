<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingFee extends Model
{
    use HasFactory;
    protected  $table = 'pending_fees';
    protected $fillable = [
        "user_id",
        "charged_id",
        'customer_id',
        'amount',
        'created_at',
        'updated_at'
    ];
}
