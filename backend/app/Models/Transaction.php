<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = "transactions";

    public $timestamps = true;
    
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'amount',
        'commission_fee'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
