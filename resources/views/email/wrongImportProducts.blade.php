@component('mail::message')
    @if(!empty($wrongProducts))
    ### Товары у которых не соответствует название или штрихкод

    @foreach($wrongProducts as $product)
Строка: {{$product['row']}} Название: {{$product['name']}} Штрихкод: {{$product['barcode']}}
    @endforeach
    @endif
    @if(!empty($newProducts))
    ### Новые товары

    @foreach($newProducts as $product)
Строка: {{$product['row']}} Название: {{$product['name']}} Штрихкод: {{$product['barcode']}}
    @endforeach
    @endif
@endcomponent
