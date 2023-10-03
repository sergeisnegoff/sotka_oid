@component('mail::message')

# Имеются изменения в заказе
Доброго времени суток! В Вашем заказе №{{ $order->id }} имеются изменения. Пожалуйста ознакомьтесь с ними в личном кабинете по ссылке <a href="{{ $_SERVER['HTTP_HOST'] }}/profile/orders/current">{{ $_SERVER['HTTP_HOST'] }}/profile/orders/current</a>

@component('mail::button', ['url' => $_SERVER['HTTP_HOST']])
Перейти на сайт
@endcomponent

С уважением,<br>
{{ config('app.name') }}
@endcomponent
