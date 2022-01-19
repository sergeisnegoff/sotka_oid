@extends('layouts.app')

@section('content')
    <div class="container mb-5" style="margin: 120px auto; text-align: center">
        <div class="card mt-5">
            <div class="card-body">
                <h2 class="card-title text-center">Заказ #{{ $order->id }} успешно создан!</h2>
                <p class="card-text text-center mt-5 mb-5">В данный момент Ваш заказ взят в обработку</p>

                <a href="{{ route('home') }}" class="btn btn-primary" style="max-width: 240px; margin: auto;">Вернуться в каталог</a>
            </div>
        </div>
    </div>

    <style></style>
@endsection
