@php
    $border = '0.3mm solid #000';
@endphp
<div style="font-size: 8mm">&nbsp;</div>
<table style="padding: 1.2mm 2mm">
    <tbody>
    <tr>
        <td colspan="2" style="border-bottom: {{$border}}; font-size: 6.2mm; width: 180mm;line-height: 125%">Перечень товаров от {{  now()->format('d.m.y') }}</td>
    </tr>
    <tr>
        <td>Покупатель:</td>
        <td>{{auth()->user()->name}}</td>
    </tr>
    <tr>
        <td>Заказ №{{$preorder->preorder->id}} - {{$preorder->preorder->title}}</td>
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
    @foreach($preorder->products as $product)
        <tr>
            <td style="border: {{$border}}; text-align: left;"> {{ $product->preorder_product->title }} </td>
            <td style="border: {{$border}}; text-align: center;">{{ $product->qty }}</td>
            <td style="border: {{$border}}; text-align: center;">шт</td>
            <td style="border: {{$border}}; text-align: center;">{{ $product->preorder_product->price }}  руб.</td>
            <td style="border: {{$border}}; text-align: center;">{{ $product->total() }}  руб.</td>
        </tr>
    @endforeach
    </tbody>
</table>
<div style="font-size: 5mm">&nbsp;</div>
<table style="padding: 1.2mm 2mm">
    <tbody>
    <tr>
        <td style="width: 146.5mm;text-align: right">Сумма:</td>
        <td style="text-align: left; width: 33.5mm">{{ $preorder->total() }}  руб.</td>
    </tr>
    <tr>
        <td style="width: 146.5mm;text-align: right">Минимальная предоплата:</td>
        <td style="text-align: left; width: 33.5mm">{{ $preorder->prepay_amount() }}  руб.</td>
    </tr>
    <tr>
        <td style="width: 146.5mm;text-align: right">Итого:</td>
                <td style="text-align: left; width: 33.5mm">{{ $preorder->total() }}  руб.</td>
    </tr>
    </tbody>
</table>
<table style="padding: 1mm 2mm">
    <tbody>
    <tr>
                <td style="border-bottom: {{$border}}">Всего наименований: {{ count($preorder->products) }}, на сумму {{ $preorder->total() }} руб.</td>
    </tr>
    </tbody>
</table>
<div style="font-size: 4.3mm">&nbsp;</div>
<table>
    <tbody>
    <tr>
        <td>
            <img style="width: 50.2mm; height: 30mm" src="{{asset('/assets/img/design/pdf/stamp.png')}}">
        </td>
        <td style="width: 29.3mm"></td>
        <td>
            <div style="font-size: 12.2mm">&nbsp;</div>
            Заказчик ____________________________
        </td>
    </tr>
    </tbody>
</table>
