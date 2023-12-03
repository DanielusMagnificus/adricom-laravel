<?php

namespace App\Http\Repositories;

use App\Models\Currency;
use App\Models\CurrencyQuote;

class CurrencyQuoteRepository {

    public function getAllQuotesForCurrency(string $currencyId) {
        return CurrencyQuote::where('currency_id', $currencyId)->get();
    }

    public function updateOrInsertQuotesForCurrency(array $worldCurrencyData) {
        CurrencyQuote::updateOrInsert([
            'name' => $worldCurrencyData['name'], 
            'currency_id' => $worldCurrencyData['currency_id']
        ], $worldCurrencyData);
    }

    public function updateCurrencyPrice(string $currencyId, float $newPrice, string $realWorldCurrencyName) {
        CurrencyQuote::where('currency_id', $currencyId)
            ->where('name', $realWorldCurrencyName)
            ->update([
                'price' => $newPrice
            ]);
    }
}