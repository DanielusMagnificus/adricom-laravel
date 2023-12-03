<?php

namespace App\Http\Services;

use App\Http\Repositories\CurrencyQuoteRepository;
use App\Http\Repositories\CurrencyRepository;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService {

    private CurrencyRepository $currencyRepository;
    private CurrencyQuoteRepository $currencyQuoteRepository;

    public function __construct(CurrencyRepository $currencyRepository, CurrencyQuoteRepository $currencyQuoteRepository) {
        $this->currencyRepository = $currencyRepository;
        $this->currencyQuoteRepository = $currencyQuoteRepository;
    }

    public function getAllCoins() {
        return $this->currencyRepository->getAllCurrencies();
    }

    public function fetchCurrenciesFromApi() {
        $apiUrl = Config::get('api.coinpaprika.base_url') . 'tickers';
        $response = Http::get($apiUrl);
        $data = $response->json();
        return $data;
    }

    public function saveCurrenciesToDb(array $currencyList) {
        $editedCurrencies = $this->currencyRepository->getAllCurrenciesByEditStatus(1);
        
        $currencyListToInsert = [];
        foreach ($currencyList as $currency) {

            if ($this->isCurrencyEdited($currency['id'], $editedCurrencies)) {
                continue;
            }

            $currencyToInsert = [
                'id' => $currency['id'],
                'name' => $currency['name'],
                'symbol' => $currency['symbol'],
                'updated_at' => now()
            ];
            
            $this->currencyRepository->updateOrInsertCurrency($currencyToInsert);

            $this->updateOrInsertQuotes($currency);
        }
    }

    /**
     * za ovu logiku znam da nije najbolja, jer se commit izvrsava kad i insert (za svaki redak).
     * pokusao sam na par nacina batch insert, ali mi nije uspjelo i dolazilo je do constraint violation-a.
     * preko doctrine u symfony bih to elegantnije rijesio, ali eloquent jos nisam u potpunosti savladao.
     */
    private function updateOrInsertQuotes(array $currency) {
        $worldCurrencies = $currency['quotes'];

        foreach ($worldCurrencies as $worldCurrencyName => $worldCurrency) {
            $worldCurrencyData = [
                'name' => $worldCurrencyName,
                'price' => $worldCurrency['price'],
                'percent_change_15m' => $worldCurrency['percent_change_15m'],
                'currency_id' => $currency['id']
            ];

            $this->currencyQuoteRepository->updateOrInsertQuotesForCurrency($worldCurrencyData);
        }   
    }

    public function getTopCurrenciesByLeastPercentChanged() {
        $currencyList = $this->currencyRepository->getTopCurrenciesByLeastPercentChanged();
        $currencyList = $this->attachQuotesToCurrenciesList($currencyList);
        return $currencyList;
    }

    public function getAllCurrencies() {
        $currencyList = $this->currencyRepository->getAllCurrencies();
        $currencyList = $this->attachQuotesToCurrenciesList($currencyList);
        return $currencyList;
    }

    public function updateCurrencyPrice(string $currencyId, float $newPrice, string $realWorldCurrencyName) {
        try {
            DB::beginTransaction();
            $this->currencyQuoteRepository->updateCurrencyPrice($currencyId, $newPrice, $realWorldCurrencyName);
            $this->currencyRepository->setCurrencyEdited($currencyId);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function saveCurrenciesFromApiToDb() {
        $currencyList = $this->fetchCurrenciesFromApi();
        $this->saveCurrenciesToDb($currencyList);
    }

    private function attachQuotesToCurrenciesList($currencyList) {
        foreach ($currencyList as $currency) {
            $currencyKey = $currency->currency_name;
            $quotes = [];
            $currency->load('quotes');

            foreach ($currency->quotes as $quote) {
                $quotes[$quote->name] = [
                    'price' => $quote->price,
                    'percent_change_15m' => $quote->percent_change_15m,
                ];
            }
            $currency->quotes = $quotes;
        }
        return $currencyList;
    }

    private function isCurrencyEdited($currencyId, $editedCurrencies) {
        return $editedCurrencies->contains('id', $currencyId);
    }
}