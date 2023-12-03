<?php

namespace App\Http\Controllers;

use App\Http\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class CurrencyController extends Controller
{

    private CurrencyService $currencyService;

    function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Lists all currencies.
     */
    public function index() {
        $currenciesList = $this->currencyService->getAllCurrencies();

        $paginator = new Paginator($currenciesList, 10);

        return view('currency.index', [
            'currencyList' => $currenciesList,
            'paginator' => $paginator
        ]);
    }

    /**
     * Updates the price of a single currency.
     */
    public function update(Request $request, string $id) {
        $request->validate([
            'currencyId' => 'required',
            'newCurrencyValue' => 'required|numeric|min:1',
            'realWorldCurrencyName' => 'required|min:3|max:3'
        ]);

        $currencyId = $request->currencyId;
        $newPrice = $request->newCurrencyValue;
        $realWorldCurrencyName = $request->realWorldCurrencyName;

        $this->currencyService->updateCurrencyPrice($currencyId, $newPrice, $realWorldCurrencyName);

        return redirect()->route('index');
    }

    /**
     * Lists top currencies ordered by percent_changed_15m.
     */
    public function topCurrencies() {
        $topCurrenciesList = $this->currencyService->getTopCurrenciesByLeastPercentChanged();

        return view('currency.index', [
            'currencyList' => $topCurrenciesList
        ]);
    }
}
