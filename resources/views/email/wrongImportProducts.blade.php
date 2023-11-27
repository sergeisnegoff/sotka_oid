@component('mail::message')
    ### Товары у которых не соответствует название или штрихкод

    @foreach($products as $product)
Строка: {{$product['row']}} Название: {{$product['name']}} Штрихкод: {{$product['barcode']}}
    @endforeach

@endcomponent
