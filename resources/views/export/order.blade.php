@php
    $border = '0.3mm solid #000';
    /** @var App\Models\Order $order */
@endphp
<table>
    <tbody>
    <tr>
        <td style="text-align: center;font-size: 3mm;height: 6mm">
            Счет Вы можете оплатить в отделении любого банка.
        </td>
    </tr>
    <tr>
        <td style="text-align: center;font-size: 3mm;height: 11mm">Счет действителен в течении 3x рабочих дней!</td>
    </tr>
    </tbody>
</table>
<table style="border: {{$border}};padding: 2mm">
    <tbody>
    <tr>
        <td colspan="2" style="border-right: {{$border}};width: 77mm;">ФИЛИАЛ "НОВОСИБИРСКИЙ" ОАО "АЛЬФАБАНК" Г.НОВОСИБИРСК</td>
        <td style="border-right: {{$border}};border-bottom: {{$border}};width: 20mm"><div style="font-size:1mm">&nbsp;</div>БИК</td>
        <td style="width: 83mm;"><div style="font-size:1mm">&nbsp;</div>045004774</td>
    </tr>
    <tr>
        <td colspan="2" style="border-right: {{$border}};border-bottom: {{$border}};"><div style="font-size:1mm">&nbsp;</div>Банк получателя<div style="font-size:1mm">&nbsp;</div></td>
        <td style="border-right: {{$border}};border-bottom: {{$border}};"><div style="font-size:1mm">&nbsp;</div>Сч.№</td>
        <td style="border-bottom: {{$border}};"><div style="font-size:1mm">&nbsp;</div>30101810600000000774</td>
    </tr>
    <tr>
        <td style="border-right: {{$border}};border-bottom: {{$border}}">ИНН 2222882928</td>
        <td style="border-right: {{$border}};border-bottom: {{$border}}">КПП 222201001</td>
        <td style="border-right: {{$border}};"></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="2" style="border-right: {{$border}};">Общество с ограниченной ответственностью "ТРИУМФ"</td>
        <td style="border-right: {{$border}};">Сч.№</td>
        <td>40702810023100003955</td>
    </tr>
    <tr>
        <td colspan="2" style="border-right: {{$border}};">Получатель</td>
        <td style="border-right: {{$border}};"></td>
        <td></td>
    </tr>
    </tbody>
</table>
<div style="font-size: 8mm">&nbsp;</div>
<table style="padding: 1.2mm 2mm">
    <tbody>
    <tr>
        <td colspan="2" style="border-bottom: {{$border}}; font-size: 6.2mm; width: 180mm;line-height: 125%">Счет на оплату №{{$order->id}}</td>
    </tr>
    <tr>
        <td style="width: 36mm;"><div style="font-size:1mm">&nbsp;</div>Поставщик:</td>
        <td style="width: 144mm">Общество с ограниченной ответственностью "Триумф", ИНН 2222882928, КПП 222201001, 656023, Алтайский край, Барнаул г, пр-т Космонавтов, 6г</td>
    </tr>
    <tr>
        <td><div style="font-size:1mm">&nbsp;</div>Грузоотправитель:</td>
        <td>Общество с ограниченной ответственностью "Триумф", ИНН 2222882928, КПП 222201001, 656023, Алтайский край, Барнаул г, пр-т Космонавтов, 6г</td>
    </tr>
    <tr>
        <td>Покупатель:</td>
        <td>{{$order->name}}</td>
    </tr>
    <tr>
        <td>Грузополучатель:</td>
        <td>{{$order->name}}</td>
    </tr>
    </tbody>
</table>
<div style="font-size: 5mm">&nbsp;</div>
<table style="border: {{$border}};padding: 1.2mm 2mm">
    <tbody>
    <tr>
        <td style="border: {{$border}}; text-align: center; width: 87.5mm">Наименование</td>
        <td style="border: {{$border}}; text-align: center; width: 19.5mm">Кол-во</td>
        <td style="border: {{$border}}; text-align: center; width: 9.5mm">Ед.</td>
        <td style="border: {{$border}}; text-align: center; width: 30mm">Цена</td>
        <td style="border: {{$border}}; text-align: center; width: 33.5mm">Сумма</td>
    </tr>
    @foreach($order->products as $product)
        <tr>
            <td style="border: {{$border}}; text-align: center;">{{$product->title}}</td>
            <td style="border: {{$border}}; text-align: center;">{{$product->pivot->qty}}</td>
            <td style="border: {{$border}}; text-align: center;">шт</td>
            <td style="border: {{$border}}; text-align: center;">{{$product->pivot->price}}</td>
            <td style="border: {{$border}}; text-align: center;">{{$product->pivot->price * $product->pivot->qty}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<div style="font-size: 5mm">&nbsp;</div>
<table style="padding: 1.2mm 2mm">
    <tbody>
    <tr>
        <td style="width: 146.5mm;text-align: right">Сумма:</td>
{{--        <td style="text-align: left; width: 33.5mm">{{format_cost($order->full)}}</td>--}}
    </tr>
    @if($order->full > $order->cost)
        <tr>
            <td style="width: 146.5mm;text-align: right">Скидка:</td>
{{--            <td style="text-align: left; width: 33.5mm">{{format_cost($order->full - $order->cost)}}</td>--}}
        </tr>
    @endif
    <tr>
        <td style="width: 146.5mm;text-align: right">Итого:</td>
{{--        <td style="text-align: left; width: 33.5mm">{{format_cost($order->cost)}}</td>--}}
    </tr>
    </tbody>
</table>
<table style="padding: 1mm 2mm">
    <tbody>
    <tr>
        <td style="border-bottom: {{$border}}">Всего наименований: {{--{{$order->count}}--}}, на сумму {{--{{format_cost($order->cost)}}--}}</td>
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
        <td>
            <div style="font-size: 12.2mm">&nbsp;</div>
            Заказчик ____________________________
        </td>
    </tr>
    </tbody>
</table>
