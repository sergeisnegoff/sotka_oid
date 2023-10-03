@php
    $border = '0.3mm solid #000';
    /** @var App\Models\Order $order */
@endphp
<div style="font-size: 8mm">&nbsp;</div>
<table style="padding: 1.2mm 2mm">
    <tbody>
    <tr>
        <td colspan="2" style="border-bottom: {{$border}}; width: 180mm;line-height: 125%">Перечень товаров от {{  now()->format('d.m.y') }}</td>
    </tr>
    <tr>
        @php
            $data = collect([
                $order->address->region ?? null,
                $order->address->city ?? null,
                $order->address->address ?? null,
                $order->address->house ?? null
            ])->filter()->join(', ');
        @endphp
        <td>Покупатель:</td>
        <td></td>
        <td></td>
        <td style="text-align: right" colspan="2">{{$order->user->name . ($data ? " ($data)" : "")}}</td>
    </tr>
    <tr>
        <td>Заказ №{{$order->id}}</td>
    </tr>
    </tbody>
</table>
<div style="font-size: 5mm">&nbsp;</div>
<table style="border: {{$border}};padding: 1.2mm 2mm">
    <tbody>
    <tr>
        <td style="border: {{$border}}; text-align: left; width: 87.5mm">Наименование</td>
        <td style="border: {{$border}}; text-align: center; width: 19.5mm">Кол-во</td>
        <td style="border: {{$border}}; text-align: center; width: 9.5mm">Ед.</td>
        <td style="border: {{$border}}; text-align: center; width: 30mm">Цена</td>
        <td style="border: {{$border}}; text-align: center; width: 33.5mm">Сумма</td>
    </tr>
    @php
        $amount = 0;
        $changes = false;
    @endphp
    @foreach($order->products as $product)
        @if(is_null($product->info))
            @php
                $amount += $product->pivot->price + $product->price_changed;
            @endphp
        @endif
        <tr>
            <td style="border: {{$border}}; text-align: left;"> {{ $product->title }} </td>
            <td style="border: {{$border}}; text-align: center;">{{ $product->pivot->qty }}</td>
            <td style="border: {{$border}}; text-align: center;">шт</td>
            <td style="border: {{$border}}; text-align: center;">{{ $product->price }}  руб.</td>
            <td style="border: {{$border}}; text-align: center;">{{ $product->pivot->price }}  руб.</td>
        </tr>
    @endforeach
    </tbody>
</table>
<div style="font-size: 5mm">&nbsp;</div>
<table style="padding: 1.2mm 2mm">
    <tbody>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td style="width: 146.5mm;text-align: right">Сумма:</td>
        <td style="text-align: left; width: 33.5mm">{{ $amount }}  руб.</td>
    </tr>
    @if($product->price_changed)
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td style="width: 146.5mm;text-align: right">Скидка:</td>
            <td style="text-align: left; width: 33.5mm">{{$product->price_changed}}  руб.</td>
        </tr>
    @endif
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td style="width: 146.5mm;text-align: right">Итого:</td>
        <td style="text-align: left; width: 33.5mm">{{ $amount }}  руб.</td>
    </tr>
    </tbody>
</table>
<table style="padding: 1mm 2mm">
    <tbody>
    <tr>
        <td style="border-bottom: {{$border}}">Всего наименований: {{ $order->products->count() }}, на сумму {{ $amount }} руб.</td>
    </tr>
    </tbody>
</table>
<div style="font-size: 4.3mm">&nbsp;</div>
<table>
    <tbody>
    <tr>
        <td>
{{--            <img style="width: 50.2mm; height: 30mm" src="{{asset('/assets/img/design/pdf/stamp.png')}}">--}}
        </td>
        <td style="width: 29.3mm"></td>
        <td></td>
        <td colspan="2" style="text-align: right">
            <div style="font-size: 12.2mm">&nbsp;</div>
            Заказчик ____________________________
        </td>
    </tr>
    </tbody>
</table>
