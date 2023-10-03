@extends('layouts.app')

@section('content')
    <section class="box__profilebasketpage">
        <div class="container">
            @include('manager.components.tabs')
            <div class="row">
                <div class="col-12">
                    @foreach ($preorders as $preorder)
                        <div class="box__ptofile-currentorder">
                            <div class="box__item" data-order-id="{{$preorder->id}}">
                                <div class="wrapper__currentorder">
                                    <div class="row">
                                        <div class="col-12 col-xl-2">
                                            <div class="box__currentorder-ordernumber"> {{ $preorder->title }}
                                            </div>
                                        </div>
                                        <div class="col-12 col-xl-2">
                                            <i>{{$preorder->created_at }}</i>
                                        </div>
                                        <div class="col-12 col-xl-4">
                                        </div>
                                        <div class="col-12 col-xl-2">
                                            Общая сумма: <b>{{$preorder->total()}}₽</b><br/>
                                            Сумма по клиентам: <b>{{$preorder->totalForCurrentManager()}}₽</b>
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
                                                @if($preorder->total() > 0)
                                                <div class="wrapper__baskets">
                                                    <div class="wrapper__baskets-title">
                                                        <div class="row">
                                                            <div class="col-12 col-xl-3"><h4>Клиент</h4></div>
                                                            <div class="col-12 col-xl-2"><h4>Сумма по предзаказу</h4></div>
                                                        </div>
                                                    </div>

                                                    @foreach($preorder->preorderCheckoutsForCurrentManager->pluck('user')->unique() as $user)
                                                        <div class="box__ptofile-currentorder">
                                                            <div class="box__item" data-order-id="{{$user->id}}">
                                                                <div class="wrapper__currentorder">
                                                                    <div class="row">
                                                                        <div class="col-12 col-xl-3">{{$user->name}}</div>
                                                                       <div class="col-12 col-xl-4">
                                                                           {{$preorder->totalByUser($user)}}₽
                                                                       </div>
                                                                        <div class="col-12 col-xl-3">
                                                                            <div class="btn">
                                                                                <a href="/manager/clients/{{$user->id}}/preorders_history">К оформлениям пользователя</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--div class="box__basketpage">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <div class="wrapper__baskets">
                                                                                <div class="wrapper__baskets-title">
                                                                                    <div class="row">
                                                                                        <div class="col-12 col-xl-3"><h4>Наименование</h4></div>
                                                                                        <div class="col-12 col-xl-1"><h4>Количество</h4></div>
                                                                                        <div class="col-12 col-xl-2"><h4>Цена</h4></div>
                                                                                        <div class="col-12 col-xl-2"><h4>Стоимость</h4></div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div-->
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                </div>
                                                @else
                                                    <div class="text-center w-100 pb-5 pt-3">
                                                Оформлений по предзаказу нет.
                                                    </div>
                                                @endif
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

