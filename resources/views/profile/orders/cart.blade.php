@extends('layouts.app')

@section('content')
    <section class="box__profilebasketpage">
        <div class="container">
            @include('profile.components.tabs')

            <div class="box__basketpage">
                @if (!empty($cart))
                    <div class="row">
                        <div class="col-12">
                            <div class="wrapper__baskets">

                                @if ($hasChanges)
                                    <div class="wrapper__baskets-warning">Имеются изменения в корзине</div>
                                @endif

                                <div class="wrapper__baskets-title">
                                    <div class="row">
                                        <div class="col-12 col-xl-3"><h4>Наименование</h4></div>
                                        <div class="col-12 col-xl-2"><h4>Количество</h4></div>
                                        <div class="col-12 col-xl-2"><h4>Цена</h4></div>
                                        <div class="col-12 col-xl-2"><h4>% скидки</h4></div>
                                        <div class="col-12 col-xl-2"><h4>Цена со скидкой</h4></div>
                                        <div class="col-12 col-xl-2"><h4>Стоимость</h4></div>
                                    </div>
                                </div>
                                <?php
                                $totalAmount = 0;
                                ?>

                                @if ($removed_products->isNotEmpty())
                                    @foreach($removed_products as $removed_product)
                                        <div class="wrapper__baskets-item changes-product deleted-product">
                                            <div class="row">
                                                <div class="col-12 col-xl-3">
                                                    <div class="wrapper__baskets-info">
                                                        <div class="box__image"><span
                                                                style="background-image: url( {{ thumbImg($removed_product['images'], 50, 70) }} );"></span>
                                                        </div>
                                                        <a href="#"><h3>{{ $removed_product->title }}</h3></a>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-xl-2">
                                                    <div class="wrapper__baskets-quality">
                                                        <span class="wrapper__baskets-titlequality">Количество:</span>
                                                        <div class="box__quality">
                                                            Нет в наличии
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-xl-2">
                                                    <div class="wrapper__baskets-price"><span>Цена:</span>
                                                        -
                                                    </div>
                                                </div>
                                                <div class="col-12 col-xl-2">
                                                    <div class="wrapper__baskets-discount"><span>Скидка:</span>
                                                        -
                                                    </div>
                                                </div>
                                                <div class="col-12 col-xl-2">
                                                    <div class="wrapper__baskets-priceondiscount">
                                                        -
                                                    </div>
                                                </div>
                                                <div class="col-12 col-xl-2">
                                                    <div class="wrapper__baskets-cost">
                                                        -
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    @endforeach
                                @endif

                                @foreach($quantity_changes as $product)
                                    <div class="wrapper__baskets-item changes-product deleted-product">
                                        <div class="row">
                                            <div class="col-12 col-xl-3">
                                                <div class="wrapper__baskets-info">
                                                    <div class="box__image"><span
                                                            style="background-image: url( {{ thumbImg($product['images'], 50, 70) }} );"></span>
                                                    </div>
                                                    <a href="#"><h3>{{ $product['title'] }}</h3></a>
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-2">
                                                <div class="wrapper__baskets-quality">
                                                    <span class="wrapper__baskets-titlequality">Количество:</span>
                                                    <div class="box__quality">
                                                        {{number_format($product['quantity_changes'], 0, ',', ' ')}} шт
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-2">
                                                <div class="wrapper__baskets-price"><span>Цена:</span>
                                                    -
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-2">
                                                <div class="wrapper__baskets-discount"><span>Скидка:</span>
                                                    -
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-2">
                                                <div class="wrapper__baskets-priceondiscount">
                                                    -
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-2">
                                                <div class="wrapper__baskets-cost">
                                                    -
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                @endforeach

                                @foreach ($cart as $product)

                                    @if (!isset($product['price'])) @continue @endif
                                    @php
                                        $id = $product['id'];
                                        $product['multiplicity'] = $productInfo[$id]->multiplicity;
                                        $percent = $productInfo[$id]->sale;
                                    @endphp
                                    <div class="wrapper__baskets-item" id="order-cart-item{{$id}}">
                                        <div class="row">
                                            <div class="col-12 col-xl-3">
                                                <div class="wrapper__baskets-info">
                                                    <div class="box__image"><span
                                                            style="background-image: url( {{ thumbImg($product['images'], 50, 70) }} );"></span>
                                                    </div>
                                                    <a href="#"><h3>{{ $product['title'] }}</h3></a>
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-2">
                                                <div class="wrapper__baskets-quality">
                                                    <span class="wrapper__baskets-titlequality">Количество:</span>
                                                    <div class="box__quality">
                                                        <div class="box__quality-value">
                                                            <input type="number"
                                                                   data-number="{{ $product['multiplicity'] }}"
                                                                   step="{{ $product['multiplicity'] }}"
                                                                   min="{{ $product['multiplicity'] }}"
                                                                   max="{{ $product['total'] }}"
                                                                   name="quantity[]" class="quantityUpdate{{$id}}"
                                                                   value="{{ $product['quantity'] }}"
                                                                   data-type="order"
                                                                   data-mode="cart"
                                                                   data-id="{{ $id }}">
                                                        </div>
                                                        @if ($productInfo[$id]->multiplicity <= $productInfo[$id]->total)
                                                            <span class="btn__quality-nav">
                                                                 <span class="btn__quality-minus update-cart"
                                                                       data-id="{{ $id }}" data-prev-quality>-</span>
                                                            <span class="btn__quality-plus update-cart"
                                                                  data-id="{{ $id }}" data-next-quality>+</span>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-2">
                                                <div class="wrapper__baskets-price"><span>Цена:</span>
                                                    {{ $productInfo[$id]->price }} ₽
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-2">
                                                <div class="wrapper__baskets-discount"><span>Скидка:</span>
                                                    {{ $percent ? : 0 }}
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-2">
                                                <div class="wrapper__baskets-priceondiscount">
                                                    <span>Цена со скидкой:</span>{{ $productInfo[$id]->price - ($percent ? (($productInfo[$id]->price * $percent) / 100) : 0) }}
                                                    ₽
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-2">
                                                <div class="wrapper__baskets-cost">
                                                    <span>Стоимость:</span>
                                                    <div class="item-amount{{$product['id']}} item-amounts" style="display: inline">
                                                    {{ ($productInfo[$id]->price - ($percent ? (($productInfo[$id]->price * $percent) / 100) : 0)) * $product['quantity'] }}
                                                    </div>
                                                    ₽
                                                </div>
                                            </div>
                                        </div>
                                        <div class="btn btn-delete remove-from-cart" data-id="{{ $id }}"><a
                                                href="javascript:;"></a></div>
                                    </div>
                                    <script>
                                        $("body").on('click', ".remove-from-cart", function () {
                                            setTimeout(()=> {
                                                if ($('.wrapper__baskets-item').length === 0) {
                                                    location.reload()
                                                }
                                            }, 700)
                                        })
                                    </script>
                                    @php($totalAmount += ($productInfo[$id]->price - ($percent ? (($productInfo[$id]->price * $percent) / 100) : 0)) * $product['quantity'])
                                @endforeach
                            </div>
                            <div class="wrapper__bascket-bottom">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="btn btn-white"><a href="{{ route('cart.empty') }}">Очистить
                                                корзину</a></div>
                                    </div>
                                    <div class="col-6">
                                        <div class="box__bascket-total">
                                            <h4><span>Итого: </span><b>
                                                    <div id="total-amount" style="display: inline">
                                                    {{ number_format($totalAmount, 0, '.', '') }}
                                                    </div>
                                                    ₽</b></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="wrapper__basket-total wrapper__basketbottom-total">
                                <div class="box__form">
                                    <form method="POST" id="order-form" action="{{ route('cart.create') }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-12 col-xl-6">
                                                <div class="row">
                                                    <div class="col-12 col-xl-6">
                                                        <h3>Заказ на магазин</h3>
                                                    </div>
                                                </div>
                                                @if($address->isNotEmpty())
                                                    @foreach ($address as $item)
                                                        <div class="box__radiobox">
                                                            <div class="wrapper-radiobox">
                                                                <label>
                                                                    <input type="radio" name="address_id"
                                                                           {{ $item->id == $user->address ? 'checked' : '' }} value="{{ $item->id }}">
                                                                    <span>
                                                                <span class="box__radiobox-icon"></span>
                                                                <span class="box__radiobox-text">
                                                                    <span
                                                                        class="box__profile-itemaddress"><span>Город: </span>{{ $item->city }}</span>
                                                                    <span
                                                                        class="box__profile-itemaddress"><span>Адрес: </span>{{ $item->address }}</span>
                                                                </span>
                                                            </span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                <div class="box__radiobox">
                                                    <div class="wrapper-radiobox">
                                                        <label>
                                                            <input type="radio" name="address_id"
                                                                   {{ 99 == $user->address ? 'checked' : '' }} value="99">
                                                            <span>
                                                                <span class="box__radiobox-icon"
                                                                      style="margin-top: 5px"></span>
                                                                    <span class="box__radiobox-text">
                                                                        <span class="box__profile-itemaddress"><strong>Самовывоз</strong></span>
                                                                    </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="btn btn__address-add">
                                                    <button data-btn-popup="address" type="button">Добавить адрес
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-6">
                                                <div class="box-bottom">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="box__textarea">
                                                                <label class="label-title">Комментарий</label>
                                                                <textarea name="comment" id=""></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="btn">
                                                                <button type="submit"{{ $user->address || !empty($address) ? '' : ' disabled' }}>
                                                                    Отправить заказ
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <h1>Корзина пуста</h1>
                @endif
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

        <div class="box__popup" data-popup="missing-address">
            <div class="wrapper-popup">
                <div class="btn__close">
                    <button aria-label="Закрыть попап" data-btn-closepopup><span></span></button>
                </div>
                <div class="row">
                    <h2>Выберите новый адрес доставки</h2>
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
    </script>

    <style>
        .autoComplete_result:hover {
            background-color: #338c0d78;
        }
    </style>

@endsection


