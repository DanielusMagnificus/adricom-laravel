<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyQuote extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'price', 'percent_change_15m'];
    public $timestamps = false;

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
