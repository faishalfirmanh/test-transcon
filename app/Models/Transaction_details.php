<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction_details extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'transaction_details';
    protected $fillable  = [
        'item_name',
        'transaction_number',
        'quantity'
    ];
}
