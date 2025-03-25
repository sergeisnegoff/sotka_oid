@extends('layouts.app')

@section('content')
    <section id="page_cart_preorder_tab" class="box__profilebasketpage">
        <div class="container">
            @include('manager.clients.components.tabs')
            <div class="row">
                <div class="col-12">
                    <div class="box__ptofile-currentorder">
                        @foreach ($cart as $order)

                           {{-- @php
                                $amount = 0;
                                $changes = false;
                                foreach ($order->products as $product) {
                                    $amount += $product->pivot->price + $product->pivot->price_changed;

                                    if ($product->pivot->qty == 0 || $product->pivot->excepted == 1)
                                    	$changes = true;
                                }
                            @endphp--}}

                            <div class="box__item" data-order-id="{{$order->id}}">
                                <div class="wrapper__currentorder">
                                    <div class="row">
                                        <div class="col-12 col-xl-3">
                                            <div class="box__currentorder-ordernumber">Предзаказ: {{ $order->preorder->title }} (заказ {{$order->id}})
                                            </div>
                                        </div>
                                        <div class="col-12 col-xl-2">

                                            <div class="box__currentorder-status">
                                                <div class="btn btn__currentorder-export">
                                                    <a href="/preorders/export-pdf/{{$order->id}}" target="_blank">Скачать перечень товаров PDF</a>
                                                </div>
                                                <div class="btn btn__currentorder-export">
                                                    <a href="/preorders/export-xls/{{$order->id}}" target="_blank">Скачать перечень товаров XLS</a>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-12 col-xl-2">
                                            <div>
                                                <span>Предоплата: <span class="prepay_amount">{{number_format($order->prepay_amount(), 2, ',', ' ')}}</span></span>
                                                <span>₽</span>
                                            </div>
                                        </div>
                                        <div class="col-6 col-xl-3">
                                            <div>
                                                <span>Стоимость: <span class="total_amount">{{number_format($order->total(), 2, ',', ' ')}}</span></span>
                                                <span>₽</span>
                                            </div>
                                        </div>
                                        <div class="col-6 col-xl-2">
                                            <div class="btn delete-preorder-btn" data-id="2188" style="margin-left:5px;">
                                                <a href="#" onclick="deletePreorder({{$order->id}})">
                                                    Удалить предзаказ
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-6 col-xl-2">
                                            @if(time() < strtotime($order->preorder->end_date))
                                                <div class="btn">
                                                    <a href="{{ route('manager.clients.clonePreorder', [$user->id,$order->id]) }}"
                                                       onclick="clonePreorder({{$order->id}})">
                                                        Повторить заказ &rang;
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="btn__currentorder-toggle">
                                        <button></button>
                                    </div>
                                </div>
                                <div class="wrapper__currentorder-info">

                                    <div class="box__basketpage">

                                        @if (count($order->products))
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="wrapper__baskets">
                                                        <div class="wrapper__baskets-title">
                                                            <div class="row">
                                                                <div class="col-12 col-xl-3"><h4>Наименование</h4></div>
                                                                <div class="col-12 col-xl-2"><h4>Количество</h4></div>
                                                                <div class="col-12 col-xl-2"><h4>Цена</h4></div>
                                                                <div class="col-12 col-xl-2"><h4>Стоимость</h4></div>
                                                            </div>
                                                        </div>
                                                        @foreach ($order->products as $product)
                                                            @php
                                                                $id = $product->id;
                                                            @endphp
                                                            <div class="wrapper__baskets-item" id="preorder-cart-item{{$id}}">
                                                                <div class="row">
                                                                    <div class="col-12 col-xl-3">
                                                                        <div class="wrapper__baskets-info">
                                                                            <div class="box__image"><span
                                                                                    style="background-image: url( {{ thumbImg($product->preorder_product->image ?? $product->preorder_product->preorder->default_image, 50, 70) }} );"></span>
                                                                            </div>
                                                                            <a href="/preorders/product/{{ $product->preorder_product->id }}">
                                                                                <h3>{{ $product->preorder_product->title }}</h3></a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-xl-2">
                                                                        <div class="wrapper__baskets-quality">
                                                                            <span class="wrapper__baskets-titlequality">Количество:</span>
                                                                            <div class="box__quality">
                                                                                <div class="box__quality-value text-center">
                                                                                    {{$product->qty}}
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-xl-2">
                                                                        <div class="wrapper__baskets-price"><span>Цена:</span>
                                                                            {{ number_format($product->preorder_product->price, 2, ',', ' ') }} ₽
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-xl-2">
                                                                        <div class="wrapper__baskets-cost">
                                                                            <span>Стоимость:</span>
                                                                            <div class="item-amount{{$product['id']}} item-amounts"
                                                                                 style="display: inline">
                                                                                {{ number_format($product->total(), 2, ',', ' ') }}
                                                                            </div>
                                                                            ₽
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <script>
                                                                $("body").on('click', ".remove-from-preorder-cart", function () {
                                                                    let box = $(this).closest('.box__item');
                                                                    let order_id = box.data('order-id');
                                                                    let prepay_amount = box.find('.prepay_amount');
                                                                    let total_amount = box.find('.total_amount');

                                                                    $(this).closest('.box__basket-item').remove();
                                                                    $(`#preorder-cart-item${$(this).data('id')}`).remove();

                                                                    setTimeout(function () {
                                                                        $.get("{{ route('preorder_cart_ajax') }}", function (result) {
                                                                            let order = result['cart'][order_id]
                                                                            console.log(order.prepay_amount);
                                                                            console.log(order.total_amount);
                                                                            prepay_amount.html(order.prepay_amount);
                                                                            total_amount.html(order.total_amount);
                                                                        }, 'json')
                                                                    }, 500);
                                                                })
                                                            </script>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <h1>Корзина пуста</h1>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="box__popup" data-popup="address">
            <div class="wrapper-popup">
                <div class="btn__close">
                    <button aria-label="Закрыть попап" data-btn-closepopup><span></span></button>
                </div>
                <div class="row" id="address-content">
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@8.2.2/dist/css/autoComplete.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@8.2.2/dist/js/autoComplete.min.js"></script>
    <script src="{{ asset('js/libs/izimodal/js/iziModal.js') }}"></script>

    <script>
        let cityID = 0,
            regionID = 0,
            streetId = 0,
            buildingId = 0;

        $(function () {
            $('[data-popup=address]').iziModal({
                width: 370,
                focusInput: false
            });

            $('[data-popup="missing-address"]').iziModal({
                width: 370,
                focusInput: false
            });

            $('body').on('click', '[data-btn-popup=address]', function () {
                if (!$(this).data('id'))
                    $.get('{{ route('profile.address.create') }}', function (result) {
                        $('#address-content').html(result);
                        $('[data-popup=address]').iziModal('open');

                        initRegionAutocomplete('.region-autocomplete', "{{ route('profile.address.autocomplete') }}", $('.region-autocomplete'));
                        initCityAutocomplete('.city-autocomplete', "{{ route('profile.address.autocomplete') }}", $('.city-autocomplete'));
                        initAddressAutocomplete('.street-autocomplete', "{{ route('profile.address.autocomplete') }}", $('.street-autocomplete'));
                        initBuildingAutocomplete('.building-autocomplete', "{{ route('profile.address.autocomplete') }}", $('.building-autocomplete'));
                    });
                else
                    $.get('{{ route('profile.address.edit') }}/' + $(this).data('id'), function (result) {
                        $('#address-content').html(result);
                        $('[data-popup=address]').iziModal('open');

                        initCityAutocomplete('.city-autocomplete', "{{ route('profile.address.autocomplete') }}", $('.city-autocomplete'));
                        initAddressAutocomplete('.street-autocomplete', "{{ route('profile.address.autocomplete') }}", $('.street-autocomplete'));
                        initBuildingAutocomplete('.building-autocomplete', "{{ route('profile.address.autocomplete') }}", $('.building-autocomplete'));
                    });
            }).on('click', '.box__profile-deleteaddress', function () {
                let _self = $(this);
                if ($(this).data('id'))
                    if (confirm('Вы действительно хотите удалить адрес?'))
                        $.post('{{ route('profile.address.delete') }}/' + $(this).data('id'), function () {
                            _self.closest('.col-12').remove();
                        })
            }).on('change', '[name=current_address]', function () {
                let _self = $(this);
                if (_self.data('id'))
                    $.post('{{ route('profile.address.change') }}/' + _self.data('id'), function () {
                    })
            }).on('click', '.city-autocomplete', function () {
            }).on('submit', '#order-form', function (e) {
                if ($('[name=address_id]:checked').length == 0) {
                    $('[data-popup="missing-address"]').iziModal('open')
                    e.preventDefault();
                }
            })
        });

        function initRegionAutocomplete(selector, route, _self) {
            let settings = {
                data: {
                    src: async function () {
                        const source = await fetch(
                            route + "?s=" + _self.val() + '&type=region'
                        );
                        const data = await source.json();
                        return data;
                    },
                    key: ["id", 'name', "city", "type"],
                    results: (list) => {
                        const filteredResults = Array.from(
                            new Set(list.map((value) => value.match))
                        ).map((city) => {
                            return list.find((value) => value.match === city);
                        });
                        if (!filteredResults.length) {
                            filteredResults.push({
                                key: 'empty',
                                match: 'Регион с таким названием не найден',
                            })
                            _self.val('');
                        }
                        return filteredResults;
                    }
                },
                cache: false,
                debounce: 800,
                selector: selector,
                onSelection: (feedback) => {
                    regionID = feedback.selection.value.id;
                    _self.parent().find('input[type=hidden]').val(feedback.selection.value.id);
                    _self.val(feedback.selection.value.name);
                },
            };

            new autoComplete(settings);
        }

        function initCityAutocomplete(selector, route, _self) {
            let settings = {
                data: {
                    src: async function () {
                        if (typeof regionID === "undefined") {
                            var regionID = _self.parent().parent().parent().prev().find('input[type=hidden]').val();
                        }
                        const source = await fetch(
                            route + "?s=" + _self.val() + '&regionId=' + regionID + '&type=city'
                        );
                        return await source.json();
                    },
                    key: ["id", 'name', "city"],
                    results: (list) => {
                        const filteredResults = Array.from(
                            new Set(list.map((value) => value.match))
                        ).map((city) => {
                            return list.find((value) => value.match === city);
                        });
                        if (!filteredResults.length) {
                            filteredResults.push({
                                key: 'empty',
                                match: 'Город с таким названием не найден',
                            })
                            _self.val('');
                        }
                        return filteredResults;
                    },
                },
                cache: false,
                debounce: 500,
                selector: selector,
                onSelection: (feedback) => {
                    if (feedback.selection.key === 'empty') {
                        _self.val('');
                    } else {
                        cityID = feedback.selection.value.id;
                        _self.parent().find('input[type=hidden]').val(feedback.selection.value.id);
                        _self.val(feedback.selection.value.name);
                    }
                },
            };

            new autoComplete(settings);
        }

        function initAddressAutocomplete(selector, route, _self) {
            var cityID = _self.parent().parent().parent().prev().find('input[type=hidden]').val();
            let settings = {
                data: {
                    src: async function () {
                        if (typeof cityID === "undefined") {
                            var cityID = _self.parent().parent().parent().prev().find('input[type=hidden]').val();
                        }
                        const source = await fetch(
                            route + "?s=" + _self.val() + "&cityId=" + cityID + '&type=address'
                        );
                        const data = await source.json();
                        return data;
                    },
                    key: ["id", 'name', "city"],
                    results: (list) => {
                        const filteredResults = Array.from(
                            new Set(list.map((value) => value.match))
                        ).map((city) => {
                            return list.find((value) => value.match === city);
                        });
                        if (!filteredResults.length) {
                            filteredResults.push({
                                key: 'empty',
                                match: 'Улица с таким названием не найдена',
                            })
                            _self.val('');
                        }

                        return filteredResults;
                    }
                },
                cache: false,
                debounce: 800,
                selector: selector,
                onSelection: (feedback) => {
                    if (feedback.selection.key === 'empty') {
                        _self.val('');
                    } else {
                        streetId = feedback.selection.value.id;
                        _self.parent().find('input[type=hidden]').val(feedback.selection.value.id);
                        _self.val(feedback.selection.value.name);
                    }
                },
            };

            new autoComplete(settings);
        }

        function initBuildingAutocomplete(selector, route, _self) {
            var streetId = _self.parent().parent().parent().prev().find('input[type=hidden]').val();
            let settings = {
                data: {
                    src: async function () {
                        if (typeof streetId === "undefined") {
                            var streetId = _self.parent().parent().parent().prev().find('input[type=hidden]').val();
                        }
                        const source = await fetch(
                            route + "?s=" + _self.val() + "&streetId=" + streetId + '&type=building'
                        );
                        const data = await source.json();
                        return data;
                    },
                    key: ["id", 'name', "city"],
                    results: (list) => {
                        const filteredResults = Array.from(
                            new Set(list.map((value) => value.match))
                        ).filter((street) => street.length <= 12).map((street) => {
                            return list.find((value) => value.match === street);
                        });

                        return filteredResults;
                    }
                },
                maxResults: 15,
                cache: false,
                debounce: 800,
                selector: selector,
                onSelection: (feedback) => {
                    _self.val(feedback.selection.value.name);
                },
            };

            new autoComplete(settings);
        }
        function deletePreorder(id)
        {
            if(confirm('Вы действительно хотите удалить предзаказ?'))
            {
                $.post('{{ url('/preorders') }}/'+ id +'/remove', function (result) {
                    console.log(result);
                });
                const boxItem = $('[data-order-id='+id+']').first()
                boxItem.remove();
            }
        }
    </script>

    <style>
        .autoComplete_result:hover {
            background-color: #338c0d78;
        }
    </style>

@endsection


