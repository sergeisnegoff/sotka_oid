@foreach($seeds as $row)

    {{ $row->title }}

@endforeach

{!! $seeds->links() !!}
