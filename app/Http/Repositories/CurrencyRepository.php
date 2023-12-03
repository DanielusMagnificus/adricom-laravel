<?php

namespace App\Http\Repositories;

use App\Models\Currency;

class CurrencyRepository {

    public function getAllCurrencies() {
        $currencyList = Currency::join('currency_quotes as cq', 'currencies.id', '=', 'cq.currency_id')
            ->orderByRaw('cq.price DESC')
            ->get(['currencies.*', 'cq.name as currency_name']);
        return $currencyList;
    }

    public function getAllCurrenciesByEditStatus(int $status = 0) {
        return Currency::select()->where('is_edited', $status)->get();
    }

    public function setCurrencyEdited(string $currencyId) : void {
        Currency::where('id', $currencyId)->update([
            'is_edited' => 1,
            'updated_at' => now()
        ]);
    }

    public function updateOrInsertCurrencyList(array $currencyList) : void {
        Currency::upsert($currencyList, ['id'], ['name', 'symbol']);
    }

    public function updateOrInsertCurrency(array $currency) : void {
        Currency::updateOrInsert(['id' => $currency['id']], $currency);
    }

    public function getTopCurrenciesByLeastPercentChanged() {
        $currencyList = Currency::join('currency_quotes as cq', 'currencies.id', '=', 'cq.currency_id')
            ->orderByRaw('ABS(cq.percent_change_15m), cq.price DESC')
            ->limit(10)
            ->get(['currencies.*', 'cq.name as currency_name']);
        return $currencyList;
    }
}