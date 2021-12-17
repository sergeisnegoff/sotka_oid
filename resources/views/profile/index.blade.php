@extends('layouts.app')

@section('content')
    <section class="box__personalarea">
        <div class="container">
            @include('profile.components.tabs')
            <div class="row">
                <div class="col-12">
                    <div class="box__personalarea-profile">
                        <div class="row">
                            @if (isset($_GET['edit']))
                                <div class="col-12 col-xl-6 col-xxl-5">
                                    <div class="row">
                                        <div class="col-12">
                                            <h2>Персональные данные</h2>
                                        </div>
                                    </div>
                                    <div class="box__form">
                                        <form method="post" action="{{ route('profile.update', ['id' => $user->id]) }}">
                                            @csrf
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="box__input">
                                                        <label class="label-title">ФИО</label>
                                                        <input type="text" name="name" value="{{ $user->name }}"
                                                               minlength="4">
                                                        @error('name')
                                                        <label class="label-error"
                                                               style="color: #ca0003; display:block;opacity:1;visibility:inherit">Поле
                                                            обязательно для заполнения</label>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="box__input">
                                                        <label class="label-title">Телефон</label>
                                                        <input id="phone-mask" type="text" name="phon"
                                                               value="{{ $user->phon }}"
                                                               disabled minlength="4">
                                                        @error('phone')
                                                        <label class="label-error"
                                                               style="color: #ca0003; display:block;opacity:1;visibility:inherit">Поле
                                                            обязательно для заполнения</label>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="box__input">
                                                        <label class="label-title">Почта</label>
                                                        <input type="text" name="email" value="{{ $user->email }}"
                                                               minlength="4">
                                                        @error('email')
                                                        <label class="label-error"
                                                               style="color: #ca0003; display:block;opacity:1;visibility:inherit">Поле
                                                            обязательно для заполнения</label>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="box__input">
                                                        <label class="label-title">Город</label>
                                                        <input type="text" name="city" value="{{ $user->city }}"
                                                               minlength="4">
                                                        @error('city')
                                                        <label class="label-error"
                                                               style="color: #ca0003; display:block;opacity:1;visibility:inherit">Поле
                                                            обязательно для заполнения</label>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="btn">
                                                        <button type="submit">Сохранить</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12"></div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="row d-xl-none">
                                        <div class="col-12">
                                            <hr/>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-12 col-xl-6 col-xxl-5">
                                    <div class="row">
                                        <div class="col-12">
                                            <h2>Персональные данные</h2>
                                        </div>
                                    </div>
                                    <div class="box__personalarea-profileinfo">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="personalarea__profileinfo-item">
                                                    <h3><span>ФИО</span>{{ $user->name }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="personalarea__profileinfo-item">
                                                    <h3><span>Телефон</span>{{ $user->phon }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="personalarea__profileinfo-item">
                                                    <h3><span>Почта</span>{{ $user->email }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="personalarea__profileinfo-item">
                                                    <h3><span>Город/Населенный пункт</span>{{ $user->city }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="btn"><a href="?edit">Изменить</a></div>
                                            </div>
                                            @if (!is_null($user->manager_table))
                                                @php($manager = \Illuminate\Support\Facades\DB::table($user->manager_table)->where('uuid', $user->manager_id)->orWhere('id', $user->manager_id)->first())
                                                @if (!is_null($manager))
                                                    <div class="col-md-12" style="margin-top: 60px;">
                                                        <h3>Ваш Менеджер</h3>

                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <img src="/storage/{{ $manager->img }}"
                                                                     style="width: 100%;" alt="">
                                                            </div>
                                                            <div class="col-md-10">
                                                                <a href="tel:{{ str_replace(['+', '-', '(', ')'], '', $manager->phone) }}">{{ $manager->phone }}</a><br/>
                                                                <a href="mailto:{{$manager->email}}">{{ $manager->email }}</a><br/>
                                                                <b>{{ $manager->name }}</b>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row d-xl-none">
                                        <div class="col-12">
                                            <hr/>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-12 col-xl-6 offset-xxl-1 col-xxl-6">
                                <div class="box__profile-address">
                                    <div class="row">
                                        <div class="col-12">
                                            <h2>Адреса магазинов</h2>
                                        </div>
                                    </div>
                                    <div class="wrapper-profile-radioaddress">
                                        <div class="row">
                                            @foreach ($address as $item)
                                                <div class="col-12">
                                                    <div class="box__radiobox">
                                                        <div class="wrapper-radiobox">
                                                            <label>
                                                                <input type="radio"
                                                                       {{ $item->id == $user->address ? 'checked' : '' }} name="current_address"
                                                                       data-id="{{ $item->id }}">
                                                                <span>
                                                                    <span class="box__radiobox-icon"></span>
                                                                    <span class="box__radiobox-text">
                                                                        <span class="box__profile-itemaddress"><span>Город: </span>{{ $item->city }}</span>
                                                                        <span class="box__profile-itemaddress"><span>Адрес: </span>{{ $item->address }}</span>
                                                                    </span>
                                                                </span>
                                                            </label>
                                                            <div class="wrapper-address">
                                                                <button class="box__profile-editaddress"
                                                                        data-btn-popup="address"
                                                                        data-id="{{ $item->id }}"></button>
                                                                <button class="box__profile-deleteaddress"
                                                                        data-id="{{ $item->id }}"></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="col-12">
                                                <div class="box__radiobox">
                                                    <div class="wrapper-radiobox">
                                                        <label>
                                                            <input type="radio"
                                                                   {{ 99 == $user->address ? 'checked' : '' }} name="current_address"
                                                                   data-id="99">
                                                            <span>
                                                                    <span class="box__radiobox-icon"
                                                                          style="margin-top: 5px"></span>
                                                                    <span class="box__radiobox-text">
                                                                        <span class="box__profile-itemaddress"><strong>Самовывоз</strong></span>
                                                                    </span>
                                                                </span>
                                                        </label>
                                                        <div class="wrapper-address">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="btn">
                                                <button data-btn-popup="address">Добавление адреса</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6 col-xxl-5">
                                <div class="row">
                                    <div class="col-12">
                                        <hr/>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h2>Изменение пароля</h2>
                                    </div>
                                </div>

                                <div class="box__form">
                                    <form method="post"
                                          action="{{ route('profile.change-password', ['id' => $user->id]) }}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="box__input">
                                                    <label class="label-title">Старый пароль</label>
                                                    <input type="password" name="old_password">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="box__input">
                                                    <label class="label-title">Новый пароль</label>
                                                    <input type="password" name="password">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="box__input">
                                                    <label class="label-title">Подтверждеие нового пароля</label>
                                                    <input type="password" name="password_confirmation">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="btn">
                                                    <button type="submit">Сохранить</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

    <script>
        let cityID = 0,
            regionID = 0;

        $(function () {
            $('[data-popup=address]').iziModal({
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
                            route + "?s=" + _self.val() + '&regionId=' + regionID
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
                            route + "?s=" + _self.val() + "&cityId=" + cityID
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/4.0.9/jquery.inputmask.bundle.min.js"></script>
    <script>
        $(() => {
            $('#phone-mask').inputmask('+7 999 999 99-99');
        })
    </script>

    <style>
        .autoComplete_result:hover {
            background-color: #338c0d78;
        }
    </style>
@endsection

