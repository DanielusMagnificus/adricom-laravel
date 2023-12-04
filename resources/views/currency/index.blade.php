<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @foreach ($currencyList as $currency)
    <div class="currency-card">
        <div class="currency-name">{{ $currency->name }}</div>
        <div class="currency-info">
            Price: <span id="price_{{ $currency->id }}">{{ $currency->quotes['USD']['price'] }}</span><br>
            Percent Change (15m): <span id="percent_change_15m_{{ $currency->id }}">{{ $currency->quotes['USD']['percent_change_15m'] }}</span>
        </div>

        <input type="text" name="new_price_{{ $currency->id }}" id="new_price_{{ $currency->id }}" placeholder="New Price">
        <button onclick="updatePrice('{{ $currency->id }}', '{{ route('currencyUpdate', ['id' => $currency->id]) }}')">Update Price</button>
    </div>
@endforeach

<form id="currencyForm" action="" method="post">
    @csrf
    @method('patch')
    <input type="hidden" name="currencyId" id="currencyId" value="" />
    <input type="hidden" name="newCurrencyValue" id="newCurrencyValue" value="" />
    <input type="hidden" name="realWorldCurrencyName" id="realWorldCurrencyName" value="" />
</form>

<script>
    function updatePrice(currencyId, elementAction) {

        document.getElementById('currencyForm').action = elementAction;

        document.getElementById('currencyId').value = currencyId;

        document.getElementById('newCurrencyValue').value = document.getElementById(`new_price_${currencyId}`).value;

        document.getElementById('realWorldCurrencyName').value = "USD";
        
        document.getElementById('currencyForm').submit();
    }
</script>

</body>
</html>