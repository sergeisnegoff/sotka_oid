@component('mail::message')

# Ваш аккаунт активирован
Здравствуйте, Ваш аккаунт успешно активирован! Теперь вы можете делать заказы на сайте <a href="{{ $_SERVER['HTTP_HOST'] }}">{{ $_SERVER['HTTP_HOST'] }}</a>

@component('mail::button', ['url' => $_SERVER['HTTP_HOST']])
Перейти на сайт
@endcomponent

С уважением,<br>
{{ config('app.name') }}
@endcomponent
