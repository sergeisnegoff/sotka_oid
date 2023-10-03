@extends('layouts.app')

@section('content')
    <section class="box__profilebasketpage">
        <div class="container">
            @include('manager.components.tabs')
            @include('manager.components.dateFilter')
                <div class="col-12">
                    @foreach ($orders as $order)
                        <div class="box__ptofile-currentorder">
                            <div class="box__item" data-order-id="{{$order->id}}">
                                <div class="wrapper__currentorder">
                                    <div class="row">
                                        <div class="col-12 col-xl-2">
                                            <div
                                                class="box__currentorder-ordernumber">{{$page == 'orders' ? 'Заказ': 'Предзаказ'}}
                                                № {{ $order->id }}
                                            </div>
                                        </div>
                                        <div class="col-12 col-xl-2">
                                            <i>{{$order->created_at }}</i>
                                        </div>
                                        <div class="col-12 col-xl-4">
                                        </div>
                                        <div class="col-12 col-xl-2">
                                            <b>{{$order->total()}}₽</b>
                                        </div>
                                        <div class="col-12 col-xl-2">
                                            {{$order->status}}
                                        </div>
                                    </div>
                                    <div class="btn__currentorder-toggle">
                                        <button></button>
                                    </div>
                                </div>
                                <div class="wrapper__currentorder-info">

                                    <div class="box__basketpage">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="wrapper__baskets">
                                                    @if($page == 'orders')
                                                        <div class="row">
                                                            <div class="col-12" style="margin: 10px 5px;">
                                                                <b>Покупатель:</b> {{$order->user->name}}<br/>
                                                                <b>Адрес доставки:</b> {{$order->deliveryAddress()}}
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="wrapper__baskets-title">
                                                        <div class="row">
                                                            <div class="col-12 col-xl-3"><h4>Наименование</h4></div>
                                                            <div class="col-12 col-xl-1"><h4>Кол-во</h4></div>
                                                            <div class="col-12 col-xl-2"><h4>Цена</h4></div>
                                                            @if ($page == 'orders')
                                                                <div class="col-12 col-xl-1"><h4>%</h4></div>
                                                                <div class="col-12 col-xl-2"><h4>Цена со скидкой</h4>
                                                                </div>
                                                            @endif
                                                            <div class="col-12 col-xl-2"><h4>Стоимость</h4></div>
                                                        </div>
                                                    </div>
                                                    @foreach($order->products as $product)

                                                        <div class="wrapper__baskets-item">
                                                            <div class="row">

                                                                <div class="col-12 col-xl-1" style="margin-left:15px;">

                                                                    <div class="box__item-img"><a href="#"><span
                                                                                style="background-image: url( {{  thumbImg($page == 'orders' ? $product->images : ($product->preorder_product->image ?? $order->preorder->default_image), 50, 70) }} )"></span></a>
                                                                    </div>

                                                                </div>
                                                                <div class="col-12 col-xl-2">
                                                                    <b>{{$product->title ?? $product->preorder_product->title}}</b>
                                                                </div>
                                                                <div class="col-12 col-xl-1">
                                                                    {{$product->qty}} шт
                                                                </div>

                                                                <div class="col-12 col-xl-2">
                                                                    {{$product->price ?? $product->preorder_product->price}}₽
                                                                </div>
                                                                @if ($page == 'orders')
                                                                    <div class="col-12 col-xl-1">
                                                                        {{$product->price_changed ? -$product->price_changed : 0}}%
                                                                    </div>
                                                                    <div class="col-12 col-xl-2">
                                                                        {{$product->pivot->price * ($product->price_changed? -$product->price_changed : 1)}}
                                                                    </div>
                                                                @endif
                                                                <div class="col-12 col-xl-2">
                                                                    @if ($page == 'orders')
                                                                        {{($product->price * ($product->price_changed ?? 1)) * $product->pivot->qty}}
                                                                    @else
                                                                        {{($product->preorder_product->price * $product->qty)}}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
                @include('manager.components.pagination')
            </div>
        </div>
    </section>

    <div class="box__popup" data-popup="address">
        <div class="wrapper-popup">
            <div class="btn__close">
                <button aria-label="Закрыть попап" data-btn-closepopup><span></span></button>
            </div>
            <div class="row" id="address-content">
            </div>
        </div>
    </div>
@endsection

@section('script')
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@8.2.2/dist/css/autoComplete.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@8.2.2/dist/js/autoComplete.min.js"></script>
    <script src="{{ asset('js/libs/izimodal/js/iziModal.js') }}"></script>

@endsection

