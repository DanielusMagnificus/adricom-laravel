<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'symbol'];

    public function quotes()
    {
        return $this->hasMany(CurrencyQuote::class, 'currency_id', 'id');    }
}
