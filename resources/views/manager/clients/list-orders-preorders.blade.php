@extends('layouts.app')

@section('content')
    <section class="box__profilebasketpage">
        <div class="container">
            @include('manager.components.tabs')
            <div class="row">
                <div class="col-12">
                    @include('manager.components.subTabs')
                    @include('manager.clients.components.filter')
                    @foreach ($managerClients as $client)
                        @if ($subPage === 'orders' ? !count($client->orders) : false)
                            @continue
                        @endif
                        <div class="box__ptofile-currentorder">
                            <div class="box__item" data-order-id="{{$client->id}}">
                                <div class="wrapper__currentorder">
                                    <div class="row">
                                        <div class="col-12 col-xl-3">
                                            <div class="box__currentorder-ordernumber">{{ $client->name }}
                                            </div>
                                        </div>
                                        <div class="col-12 col-xl-3">
                                            <i>{{$client->address ?? 'адрес не указан'}}</i>
                                        </div>
                                        <div class="col-12 col-xl-4">
                                            <b>Сумма заказов по клиенту:</b> {{$subPage == 'orders' ? $client->ordersTotal() : $client->preordersTotal()}}₽
                                        </div>
                                        <div class="col-12 col-xl-2">
                                            <div class="btn">
                                                @if ($subPage === 'preorders')
                                                <div class="btn" >
                                                    <a href="/manager/clients/{{$client->id}}/upload_xlsx/">
                                                        Перейти к выгрузке предзаказов
                                                    </a>
                                                </div>
                                                @elseif ($subPage === 'orders')
                                                    <div class="btn" >
                                                        <a href="/manager/clients/{{$client->id}}/orders_history/">
                                                            Кабинет пользователя
                                                        </a>
                                                    </div>
                                                    @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!--div class="btn__currentorder-toggle">
                                        <button></button>
                                    </div-->
                                </div>
                                <!--div class="wrapper__currentorder-info">

                                    <div class="box__basketpage">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="wrapper__baskets">
                                                    <div class="wrapper__baskets-title">
                                                        <div class="row">
                                                            <div class="col-12 col-xl-3"><h4>Наименование</h4></div>
                                                            <div class="col-12 col-xl-3"><h4>Дата</h4></div>
                                                            <div class="col-12 col-xl-3"><h4>Сумма заказа</h4></div>
                                                        </div>
                                                    </div>
                                                    @foreach($subPage == 'orders' ? $client->orders : $client->preorderCheckouts as $order)
                                                        <div class="wrapper__baskets-item">
                                                            <div class="row">
                                                                <div class="col-12 col-xl-3">
                                                                    {{$subPage === 'orders' ? 'Заказ':'Предзаказ'}} №{{$order->id}}
                                                                </div>
                                                                <div class="col-12 col-xl-3">
                                                                    {{$order->created_at}}
                                                                </div>
                                                                <div class="col-12 col-xl-3">
                                                                    {{$order->total()}}₽
                                                                </div>
                                                                <div class="col-12 col-xl-3">
                                                                    @if($subPage == 'orders')
                                                                    <div class="btn">

                                                                        <a href="{{route('manager.clients.showOrder', [$client->id, $order->id])}}">
                                                                        Перейти в заказ
                                                                        </a>
                                                                    </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div-->
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @include('manager.components.pagination')
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

