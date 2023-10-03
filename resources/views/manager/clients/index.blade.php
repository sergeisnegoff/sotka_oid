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

                                        </div>
                                        <div class="col-12 col-xl-2">
                                            <div class="btn">

                                                <button type="submit" >
                                                    <a href="/manager/clients/{{$client->id}}/upload_xlsx/">
                                                        Профиль пользователя
                                                    </a>
                                                </button>

                                            </div>
                                        </div>
                                    </div>

                                </div>
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

