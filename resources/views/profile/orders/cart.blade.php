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
                                @foreach ($cart as $id => $product)
                                    @php
                                    $productInfo = \App\Product::multiplicity()->find($id);
                                        $product['multiplicity'] = $productInfo->multiplicity;
                                        $percent = \App\Product::getMaxSaleToProduct($id, $product['price'], $product['quantity'])
                                    @endphp
                                    <div class="wrapper__baskets-item">
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
                                                            <input type="number" data-number="{{ $product['multiplicity'] }}"
                                                                   step="{{ $product['multiplicity'] }}" min="{{ $product['multiplicity'] }}"
                                                                   name="quantity[]" class="quantityUpdate{{$id}}"
                                                                   value="{{ $product['quantity'] }}" data-id="{{ $id }}" readonly="">
                                                        </div>
{{--                                                        @if ($productInfo->multiplicity <= $productInfo->total)--}}
                                                            <span class="btn__quality-nav">
                                                                 <span class="btn__quality-minus update-cart" data-id="{{ $id }}" data-prev-quality>-</span>
                                                            <span class="btn__quality-plus update-cart" data-id="{{ $id }}" data-next-quality>+</span>
                                                            </span>
{{--                                                        @endif--}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-2">
                                                <div class="wrapper__baskets-price"><span>Цена:</span>
                                                    {{ $product['price'] }} ₽
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-2">
                                                <div class="wrapper__baskets-discount"><span>Скидка:</span>
                                                    {{ $percent ? : 0 }}
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-2">
                                                <div class="wrapper__baskets-priceondiscount">
                                                    <span>Цена со скидкой:</span>{{ $product['price'] - ($percent ? (($product['price'] * $percent) / 100) : 0) }}
                                                    ₽
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-2">
                                                <div class="wrapper__baskets-cost">
                                                    <span>Стоимость:</span>{{ ($product['price'] - ($percent ? (($product['price'] * $percent) / 100) : 0)) * $product['quantity'] }}
                                                    ₽
                                                </div>
                                            </div>
                                        </div>
                                        <div class="btn btn-delete remove-from-cart" data-id="{{ $id }}"><a href="javascript:;"></a></div>
                                    </div>
                                    @php($totalAmount += ($product['price'] - ($percent ? (($product['price'] * $percent) / 100) : 0)) * $product['quantity'])
                                @endforeach
                            </div>
                            <div class="wrapper__bascket-bottom">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="btn btn-white"><a href="{{ route('cart.empty') }}">Очистить корзину</a></div>
                                    </div>
                                    <div class="col-6">
                                        <div class="box__bascket-total">
                                            <h4><span>Итого: </span><b>{{ number_format($totalAmount, 0, '.', '') }} ₽</b></h4>
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
                                                @foreach ($address as $item)
                                                    <div class="box__radiobox">
                                                        <div class="wrapper-radiobox">
                                                            <label>
                                                                <input type="radio" name="address_id" {{ $item->id == $user->address ? 'checked' : '' }} value="{{ $item->id }}">
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
                                                <div class="box__radiobox">
                                                    <div class="wrapper-radiobox">
                                                        <label>
                                                            <input type="radio" name="address_id" {{ 99 == $user->address ? 'checked' : '' }} value="99">
                                                            <span>
                                                                <span class="box__radiobox-icon" style="margin-top: 5px"></span>
                                                                    <span class="box__radiobox-text">
                                                                        <span class="box__profile-itemaddress"><strong>Самовывоз</strong></span>
                                                                    </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="btn btn__address-add">
                                                    <button data-btn-popup="address" type="button">Добавить адрес</button>
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
                                                                <button {{ $user->address || !empty($address) ? : 'disabled' }}>Отправить заказ</button>
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
        let cityID = 0;
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
                        initAddressAutocomplete('.address-autocomplete', "{{ route('profile.address.autocomplete') }}", $('.address-autocomplete'))
                    });
                else
                    $.get('{{ route('profile.address.edit') }}/' + $(this).data('id'), function (result) {
                        $('#address-content').html(result);
                        $('[data-popup=address]').iziModal('open');

                        initCityAutocomplete('.city-autocomplete', "{{ route('profile.address.autocomplete') }}", $('.city-autocomplete'));
                        initAddressAutocomplete('.address-autocomplete', "{{ route('profile.address.autocomplete') }}", $('.address-autocomplete'))
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
                            route + "?s=" + _self.val()
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

                        return filteredResults;
                    }
                },
                cache: false,
                debounce: 800,
                selector: selector,
                onSelection: (feedback) => {
                    regionID = feedback.selection.value.id;
                    _self.val(feedback.selection.value.name);
                },
            };

            new autoComplete(settings);
        }
        function initCityAutocomplete(selector, route, _self) {
            let settings = {
                data: {
                    src: async function () {
                        const source = await fetch(
                            route + "?s=" + _self.val() + '&regionId='+regionID
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

                        return filteredResults;
                    }
                },
                cache: false,
                debounce: 800,
                selector: selector,
                onSelection: (feedback) => {
                    cityID = feedback.selection.value.id;
                    _self.val(feedback.selection.value.name);
                },
            };

            new autoComplete(settings);
        }
        function initAddressAutocomplete(selector, route, _self) {
            let settings = {
                data: {
                    src: async function () {
                        const source = await fetch(
                            route + "?s=" + _self.val()+"&cityId="+cityID
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

                        return filteredResults;
                    }
                },
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


