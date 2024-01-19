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
                            <form method="POST" id="order-form" class="box__item" data-order-id="{{$order['id']}}" action="{{ route('preorder_create_for_user', $user->id) }}">
                                @csrf
                                <input type="hidden" name="preorder_id" value="{{ $order['id'] }}">
                                <div class="wrapper__currentorder">
                                    <div class="row">
                                        <div class="col-12 col-xl-3">
                                            <div class="box__currentorder-ordernumber">Предзаказ: {{ $order['title'] }}
                                            </div>
                                        </div>
                                        <div class="col-12 col-xl-1">

                                        </div>
                                        <div class="col-12 col-xl-4">
                                            <div>
                                                <span>Предоплата: <span class="prepay_amount">{{$order['prepay_amount']}}</span></span>
                                                <span>₽</span>
                                            </div>
                                        </div>
                                        <div class="col-6 col-xl-2">
                                            <div>
                                                <span>Стоимость: <span class="total_amount">{{$order['total_amount']}}</span></span>
                                                <span>₽</span>
                                            </div>
                                        </div>
                                        <div class="col-6 col-xl-2">
                                            <div class="box__currentorder-status">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="btn__currentorder-toggle">
                                        <button type="button"></button>
                                    </div>
                                </div>
                                <div class="wrapper__currentorder-info">
                                    <div class="box__basketpage">
                                        @if (!empty($cart))
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="wrapper__baskets">
                                                        <div class="wrapper__baskets-title">
                                                            <div class="row">
                                                                <div class="col-12 col-xl-4">
                                                                    <div class="btn btn-white">
                                                                        <a href="{{route('preorder_category_page', $order['id'])}}">&lang; В каталог</a>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-xl-4 text-center">
                                                                    <div class="btn btn-white">
                                                                        <a href="{{route('preorder_empty_user_cart', [$order['id'], $user->id])}}">Удалить предзаказ</a>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-xl-4 text-right">
                                                                    <div class="btn">
                                                                        <button type="submit">
                                                                            Заказать &rang;
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="wrapper__baskets-title">
                                                            <div class="row">
                                                                <div class="col-12 col-xl-3"><h4>Наименование</h4></div>
                                                                <div class="col-12 col-xl-2"><h4>Количество</h4></div>
                                                                <div class="col-12 col-xl-2"><h4>Цена</h4></div>
                                                                <div class="col-12 col-xl-2"><h4>Стоимость</h4></div>
                                                            </div>
                                                        </div>
                                                        @foreach ($order['products'] as $key => $product)
                                                            @php
                                                                $id = $product['id'];
                                                            @endphp
                                                            <div class="wrapper__baskets-item" id="preorder-cart-item{{$id}}">
                                                                <div class="row">
                                                                    <div class="col-12 col-xl-3">
                                                                        <div class="wrapper__baskets-info">
                                                                            <div class="box__image"><span
                                                                                    style="background-image: url( {{ thumbImg($product['image'], 50, 70) }} );"></span>
                                                                            </div>
                                                                            <a href="/preorders/product/{{ $product['id'] }}">
                                                                                <h3>{{ $product['name'] }}</h3></a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-xl-2">
                                                                        <div class="wrapper__baskets-quality">
                                                                            <span class="wrapper__baskets-titlequality">Количество:</span>
                                                                            <div class="box__quality">
                                                                                <div class="box__quality-value">
                                                                                    <input type="hidden" name="product[{{$key}}]['product_id']" value="{{ $product['id'] }}">
                                                                                    <input type="number"
                                                                                           data-number="{{ $product['multiplicity'] }}"
                                                                                           step="{{ $product['multiplicity'] }}"
                                                                                           min="{{ $product['multiplicity'] }}"
                                                                                           name="product[{{$key}}]['quantity']" class="cart_preorder_product quantityUpdate{{$id}}"
                                                                                           value="{{ $product['quantity'] }}"
                                                                                           data-type="preorder"
                                                                                           data-mode="cart"
                                                                                           data-id="{{ $id }}">
                                                                                </div>
                                                                                <span class="btn__quality-nav">
                                                                                     <span class="decrement_product_quantity_in_cart btn__quality-minus update-cart"
                                                                                           data-id="{{ $id }}">-</span>
                                                                                    <span class="increment_product_quantity_in_cart btn__quality-plus update-cart"
                                                                                           data-id="{{ $id }}">+</span>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-xl-2">
                                                                        <div class="wrapper__baskets-price"><span>Цена:</span>
                                                                            {{ $product['price'] }} ₽
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12 col-xl-2">
                                                                        <div class="wrapper__baskets-cost">
                                                                            <span>Стоимость:</span>
                                                                            <div class="item-amount{{$product['id']}} item-amounts"
                                                                                 style="display: inline">
                                                                                {{ round($product['price'] * $product['quantity'], 2) }}
                                                                            </div>
                                                                            ₽
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="btn btn-delete remove-from-preorder-cart" data-id="{{ $id }}"><a
                                                                        href="javascript:;"></a></div>
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
                                                                            prepay_amount.html(Math.round(order.prepay_amount));
                                                                            total_amount.html(Math.round(order.total_amount));
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
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="box__popup" data-popup="address">
            <div class="wrapper-popup">
                <div class="btn__close">
                    <button type="button" aria-label="Закрыть попап" data-btn-closepopup><span></span></button>
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
            $('body').on('change', '.cart_preorder_product', function () {
                let _self = $(this);
                let product_id = $(this).data('id');

                $.post("{{ route('cart.updatePreOrderQtyForUser', $user->id) }}", {id: product_id, qty: $(this).val()}, function (result) {
                    $.get("{{ route('preorder_user_cart_ajax', $user->id) }}", function (result) {
                        let box = _self.closest('.box__item');
                        let order_id = box.data('order-id');
                        let block = _self.closest('.wrapper__baskets-item');

                        let order = result['cart'][order_id]
                        let products = order.products

                        let cur_product = products.find(function (product) {
                            return product.id === product_id;
                        });

                        block.find('.item-amounts').html(cur_product.total);
                        _self.val(cur_product.quantity);

                        box.find('.prepay_amount').html(order.prepay_amount);
                        box.find('.total_amount').html(order.total_amount);
                    }, 'json')
                }, 'json')
            }).on('click', '.decrement_product_quantity_in_cart', function () {
                let block = $(this).closest('.wrapper__baskets-item');
                let product_id = $(this).data('id');
                let box = block.closest('.box__item');
                let order_id = box.data('order-id');
                let input = block.find('.cart_preorder_product');
                let item_amounts = block.find('.item-amounts');
                let prepay_amount = box.find('.prepay_amount');
                let total_amount = box.find('.total_amount');
                let step = input.attr('step')
                let qty = parseInt(input.val()) - parseInt(step);
                input.val(qty);

                $.post("{{ route('cart.updatePreOrderQtyForUser', $user->id) }}", {id: product_id, qty: qty}, function (result) {
                    $.get("{{ route('preorder_user_cart_ajax', $user->id) }}", function (result) {
                        let order = result['cart'][order_id]
                        let products = order.products

                        let cur_product = products.find(function (product) {
                            return product.id === product_id;
                        });

                        item_amounts.html(cur_product.total);
                        input.val(cur_product.quantity);

                        prepay_amount.html(Math.round(order.prepay_amount));
                        total_amount.html(Math.round(order.total_amount));
                    }, 'json')
                }, 'json')
            }).on('click', '.increment_product_quantity_in_cart', function () {
                let block = $(this).closest('.wrapper__baskets-item');
                let product_id = $(this).data('id');
                let box = block.closest('.box__item');
                let order_id = box.data('order-id');
                let input = block.find('.cart_preorder_product');
                let item_amounts = block.find('.item-amounts');
                let prepay_amount = box.find('.prepay_amount');
                let total_amount = box.find('.total_amount');
                let step = input.attr('step')
                let qty = parseInt(input.val()) + parseInt(step);
                input.val(qty);

                $.post("{{ route('cart.updatePreOrderQtyForUser', $user->id) }}", {id: product_id, qty: qty}, function (result) {
                    $.get("{{ route('preorder_user_cart_ajax', $user->id) }}", function (result) {
                        let order = result['cart'][order_id]
                        let products = order.products

                        let cur_product = products.find(function (product) {
                            return product.id === product_id;
                        });

                        item_amounts.html(cur_product.total);
                        input.val(cur_product.quantity);

                        prepay_amount.html(Math.round(order.prepay_amount));
                        total_amount.html(Math.round(order.total_amount));
                    }, 'json')
                }, 'json')
            })

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
    </script>

    <style>
        .autoComplete_result:hover {
            background-color: #338c0d78;
        }
    </style>

@endsection


