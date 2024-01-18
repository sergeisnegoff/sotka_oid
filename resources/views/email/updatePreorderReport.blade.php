@component('mail::message')
    @if(!empty($noBarcodeRows))
        ### Строки в которых отсутствует штрихкод

        @foreach($noBarcodeRows as $row)
            Строка: {{ $row }}
        @endforeach
    @endif
    @if(!empty($noProductsRows))
        ### Не найдены товары для строк

        @foreach($noProductsRows as $row)
            Строка: {{$row['row']}} Штрихкод: {{$row['barcode']}}
        @endforeach
    @endif
@endcomponent
