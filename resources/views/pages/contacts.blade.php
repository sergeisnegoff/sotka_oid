@extends('layouts.app')

@section('content')
    <main>
        <div class="box__breadcrumbs">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <ul>
                            <li><a href="/">Главная</a></li>
                            <li>{{ $info->title }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <section class="box__page-contact">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2>Контакты отдела продаж</h2>
                    </div>
                </div>
                <div class="box__contact-personal">
                    <div class="row">
                        <div class="col-12"><h3>{{ setting('site.CONTACTS_SUPERVISOR') }}</h3></div>
                    </div>
                    <div class="row wrapper__personal-row">
                        @foreach ($contactsSupervisor as $contact)
                            <div class="col-12 col-md-7 col-xl-6 col-xxl-4">
                                <div class="box__contact-item">
                                    <div class="row">
                                        <div class="col-12 col-md-6 col-xl-6 col-xxl-6 flex__wrapper-center">
                                            <div class="box__image general"><span
                                                    style="background-image: url({{ thumbImg($contact->img, 180, 180) }});"></span>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-xl-6 col-xxl-6 supervisor">
                                            <div class="box__contact-link phone"><a
                                                    href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                                            </div>
                                            <div class="box__contact-link"><a
                                                    href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                            </div>
                                            <h4>{{ $contact->name }}<span>{{ $contact->position }}</span></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-12"><h3>{{ setting('site.CONTACTS_MANAGERS') }}</h3></div>
                    </div>
                    <div class="row wrapper__personal-row">
                        @foreach ($contactsManagers as $contact)
                            <div class="col-12 col-md-6 col-xl-4 col-xxl-3">
                                <div class="box__contact-item">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="box__image"><span
                                                    style="background-image: url({{ thumbImg($contact->img, 90, 90)  }});"></span>
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <div class="box__contact-link phone"><a
                                                    href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                                            </div>
                                            <div class="box__contact-link"><a
                                                    href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <h4>{{ $contact->name }}<span>{{ $contact->position }}</span></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="box__contact-map">
                    <div class="row">
                        <div class="col-12 col-xl-6">
                            <div class="box__map" id="map" data-map="img/icon/map.svg"></div>
                        @if (!empty($info->coordinates))
                            <!-- Вставлен тестовый API ключ -->
                                <script
                                    src="https://api-maps.yandex.ru/2.1/?apikey={{ env('API_KEY_YM') }}d&lang=ru_RU"
                                    type="text/javascript">
                                </script>
                                <script type="text/javascript">

                                    var mapIcon = map.getAttribute('data-map');

                                    ymaps.ready(init);

                                    @php($coordinates = explode(',',$info->coordinates))
                                    function init() {
                                        var map = new ymaps.Map("map", {
                                                center: [ {{ $coordinates[0] }}, {{ $coordinates[1] }}],
                                                controls: [],
                                                zoom: 15
                                            }),

                                            placemark = new ymaps.Placemark(map.getCenter(), {
                                                //content
                                            }, {
                                                iconLayout: 'default#image',
                                                iconImageHref: mapIcon,
                                                iconImageSize: [63, 80],
                                                iconImageOffset: [-31, -75]
                                            });

                                        map.geoObjects.add(placemark);
                                    }
                                </script>
                            @endif
                        </div>
                        <div class="col-12 col-xl-6">
                            <div class="contacts_slider">
                                @foreach (json_decode($info->gallery) as $img)
                                    <div class="box__image"><a class="box__full-image" href="/storage/{{ $img }}"><img src="{{ thumbImg($img, 695, 420, true) }}"></a>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
                <div class="box__contact-companyinformation">
                    <div class="row">
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="box__companyinformation-item">
                                <h4>{{ setting('site.PHONE') }}</h4>
                                <div class="box__link">
                                    <a href="tel:{{ setting('site.CONTACTS_MANAGERS_PHONE') }}">{{ setting('site.CONTACTS_MANAGERS_PHONE') }}</a>
                                    <span>Региональный отдел продаж</span>
                                </div>
                                <div class="box__link">
                                    <a href="tel:{{ setting('site.CONTACTS_SUPERVISOR_PHONE') }}">{{ setting('site.CONTACTS_SUPERVISOR_PHONE') }}</a>
                                    <span>Отдел продаж по Алтайскому краю</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="box__companyinformation-item">
                                <h4>{{ setting('site.ADDRESS') }}</h4>
                                <div class="box__companyinformation-address">
                                    {{ setting('site.ADDRESS_TEXT') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="box__companyinformation-item">
                                <h4>{{ setting('site.EMAIL') }}</h4>
                                <div class="box__link">
                                    <a href="mailto:{{ setting('site.CONTACTS_EMAIL') }}">{{ setting('site.CONTACTS_EMAIL') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="box__companyinformation-item">
                                <h4>{{ setting('site.SCHEDULE') }}</h4>
                                <div class="box__companyinformation-address">
                                    {{ setting('site.SCHEDULE_TEXT') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('script')

    <link rel="stylesheet" href="{{ asset('assets/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/slick-theme.css') }}">
    <script src="{{asset('assets/js/slick.min.js')}}"></script>

    <link rel="stylesheet" href="{{ asset('js/libs/gallery/magnific-popup.css') }}">
    <script src="{{ asset('js/libs/gallery/jquery.magnific-popup.js') }}"></script>

    <script>
        $(function () {
            $('.contacts_slider').slick({
                dots: true
            }).magnificPopup({type:'image',delegate:'a'});

        })
    </script>

    <style>
        .slick-dots {
            position: absolute;
            bottom: 15px;
        }
        .slick-dots > li button {
            width: 12px;
            height: 12px;
            border: 1px solid var(--color-7);
            background-color: transparent;
            opacity: 1;
            transition: background ease-out .2s;
            border-radius: 50%;
        }
        .slick-dots > li button:before {
            display:none
        }
        .slick-dots > li.slick-active button {
            background-color: white;
        }
    </style>
@endsection
