@extends('layouts.app')

@section('content')
    <section id="page_cart_preorder_tab" class="box__profilebasketpage">
        <div class="container">
                @include('profile.components.tabs')
            <div class="row">
                <div class="col-12">
                    <div class="box__ptofile-currentorder">
                        @foreach ($preorders as $preorder)
                            <div class="row">
                                <div class="col-3">
                                    <b>{{$preorder->title}}</b>
                                    @if(count(\App\Models\PreorderCheckout::userCheckoutsForPreorder($preorder)))
                                        <b style="color:red">*</b>
                                    @endif
                                </div>
                                <div class="col-2">
                                  @if($preorder->client_file !== '[]')
                                        <div class="btn" ><a
                                            style="font-size:1rem;"
                                            href="/storage/{{(json_decode($preorder->client_file, JSON_OBJECT_AS_ARRAY)[0]['download_link'])}}">
                                            Скачать прайс-лист</a></div>
                                    @endif
                                </div>
                                <div class="col-3">
                                    <input class="preorder-file" name="preorder_file"
                                           data-preorder-id="{{$preorder->id}}" type="file">
                                </div>
                                <div class="col-3">
                                    <div type="button" class="btn preorder-upload-btn" data-preorder-id="{{$preorder->id}}">
                                        <a href="#"  style="font-size:1rem;" class="preorder-upload" data-preorder-id="{{$preorder->id}}">Загрузить файл</a>

                                    </div>
                                    <div data-preorder-id="{{$preorder->id}}" class="preorder-upload-status" style="display:none">

                                    </div>
                                </div>
                            </div>


                        @endforeach
                            <div class="row">
                                <div class="col-6" style="margin-top: 5px;">

                                        <i><b style="color:red">*</b> - уже имеется оформленный предзаказ. При выгрузке по этому предзаказу создастся дополнительное оформление с указанными в файле позициями.</i>

                                </div>
                            </div>
                            <script>
                                $(document).ready(function () {
                                    $('.preorder-upload').click(function (e) {
                                        let btn = e.target
                                        let preorderId = $(e.target).attr('data-preorder-id')
                                        let fileSelector = $(`.preorder-file[data-preorder-id="${preorderId}"]`)[0]
                                        console.log(fileSelector)
                                        const formData = new FormData();
                                        formData.append('file', fileSelector.files[0])
                                        formData.append('preorder_id', preorderId)

                                        $(`.preorder-upload-btn[data-preorder-id="${preorderId}"]`).text('Выгрузка...')
                                        fetch(`/preorders/upload_file`, {
                                            method: "POST",
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ @csrf_token() }}'
                                            },
                                            body: formData
                                        })
                                            .then((resp) => {
                                                resp.text().then((text) => {
                                                    let res = JSON.parse(text).result
                                                    $(`.preorder-upload-btn[data-preorder-id="${preorderId}"]`).hide()
                                                    $(`.preorder-upload-status[data-preorder-id="${preorderId}"]`).show().text(res)
                                                })

                                            })
                                    })
                                })
                            </script>
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

                $.post("{{ route('cart.updatePreOrderQty') }}", {
                    id: product_id,
                    qty: $(this).val()
                }, function (result) {
                    $.get("{{ route('preorder_cart_ajax') }}", function (result) {
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

                $.post("{{ route('cart.updatePreOrderQty') }}", {id: product_id, qty: qty}, function (result) {
                    $.get("{{ route('preorder_cart_ajax') }}", function (result) {
                        let order = result['cart'][order_id]
                        let products = order.products

                        let cur_product = products.find(function (product) {
                            return product.id === product_id;
                        });

                        item_amounts.html(cur_product.total);
                        input.val(cur_product.quantity);

                        prepay_amount.html(order.prepay_amount);
                        total_amount.html(order.total_amount);
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

                $.post("{{ route('cart.updatePreOrderQty') }}", {id: product_id, qty: qty}, function (result) {
                    $.get("{{ route('preorder_cart_ajax') }}", function (result) {
                        let order = result['cart'][order_id]
                        let products = order.products

                        let cur_product = products.find(function (product) {
                            return product.id === product_id;
                        });

                        item_amounts.html(cur_product.total);
                        input.val(cur_product.quantity);

                        prepay_amount.html(order.prepay_amount);
                        total_amount.html(order.total_amount);
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


