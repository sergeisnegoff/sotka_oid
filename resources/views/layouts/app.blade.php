<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('site.name', 'Золотая сотка алтая') }}</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/app.css?' . 2) }}" rel="stylesheet">

    <!-- Favicon -->
    <?php $admin_favicon = Voyager::setting('admin.icon_image', ''); ?>
    <?php if($admin_favicon == ''): ?>
    <link rel="shortcut icon" href="<?php echo e(voyager_asset('images/logo-icon.png')); ?>" type="image/png">
    <link rel="shortcut icon" href="<?php echo e(asset('img/image/favicon.ico')); ?>">
    <?php else: ?>
    <link rel="shortcut icon" href="<?php echo e(Voyager::image($admin_favicon)); ?>" type="image/png">
    <link rel="shortcut icon" href="<?php echo e(asset('img/image/favicon.ico')); ?>">
    <?php endif; ?>

    <script src="{{asset('js/jquery-3.5.1.js')}}"></script>
    <script src="{{asset('assets/js/jquery-ui.js')}}"></script>
    <script>
        window.preorder_minimal = {{$preorder_minimal ?? 0}}
    </script>
    <script src="{{ asset('js/common.js') }}"></script>

    @stack('styles')
</head>
<body>
<script>

    @if (in_array(\Illuminate\Support\Facades\Request::segment(1), ['login', 'register']))
        window.location.href = '/?popup={{ \Illuminate\Support\Facades\Request::segment(1) }}';
    @endif

    @if (isset($_GET['popup']))
    @switch($_GET['popup'])
    @case('login')
    $('[data-popup=authorization]').iziModal('open');
    @break

    @case('register')
    $('[data-popup=registration]').iziModal('open');
    @break
    @endswitch
    @endif
</script>
<header>
    <div class="box__header-top">
        <div class="container">
            <div class="row">
                <div class="order-1 order-md-1 col-3 col-md-2 col-lg-2 col-xl-1">
                    <?php $site_logo_img = Voyager::setting('site.logo', ''); ?>
                    <div class="box__logo"><a href="/"><img src="{{ Voyager::image($site_logo_img) }}" alt=""></a></div>
                </div>
                <div class="order-2 order-md-2 col-2 col-md-4 col-lg-4 d-xl-none">
                    <div class="btn-nav">
                        <button data-btn-popup="navigation"><span></span><span></span>Меню</button>
                    </div>
                </div>
                <div class="order-4 order-md-3 col-12 d-none d-xl-block col-xl-3">
                    <div class="box__search">
                        <div class="box__form">
                            <form action="{{ route('searchProducts') }}" method="GET" role="search">
                                <div class="box__input"><input type="text" name="products" class="searchTitle"
                                                               placeholder="Найти товар"></div>
                                <input type="hidden" name="ajax-req" id="ajax-req" value="1">
                                <div id="productData">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="order-5 order-md-4 col-12 d-none d-xl-block col-xl-5">
                    <div class="box__nav-sub">
                        {{ menu('header', 'layouts.headerMenu') }}
                    </div>
                </div>
                <div class="order-3 order-md-5 col-7 col-md-6 col-lg-6 col-xl-3">
                    <div class="wrapper__header-right" style="flex-wrap: wrap">
                        @guest
                            @if (Route::has('login'))
                                <div class="box__personalarea d-md-block">
                                    <a class="nav-link" href="javascript:;" data-btn-popup="authorization"
                                       style="color:black">{{setting('site.login')}}</a>
                                </div>
                            @endif
                        @else
                            <div class="box__personalarea d-none d-sm-block d-md-flex">
                                <button onclick="location.href = '{{ route('profile.index') }}'"><span
                                        class="head-icon"></span>
                                </button>

                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   style="width: 100%;display:flex;text-align: center;color:red;text-decoration:none;align-items:center"
                                   onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                    Выйти
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        @endguest
                        @if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->active != "off")
                            <div
                                class="box__card {{ count(session('cart', [])) || count(\App\Services\Preorder\PreorderService::getCart())  ? '' : 'd-none' }}">
                                <button class="d-flex" style="align-items:center;" data-btn-popup="basket">
                                    <span class="d-block" style="margin-right: 20px;" id="total-price">
                                           {{ number_format(collect(session('cart', []))->sum(function ($item) {
                                            return ($item['price'] ?? 0) * $item['quantity'];
                                        }) + collect(\App\Services\Preorder\PreorderService::getCart())->sum(function ($item) {
                                            return ($item['price'] ?? 0) * $item['quantity'];
                                        }), 0, ',', '') }} ₽
                                    </span>
                                    <span class="head-icon">
                                        <span
                                            class="box__card-quality">{{ count(session('cart', [])) || count(\App\Services\Preorder\PreorderService::getCart()) }}</span>
                                    </span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box__header-center d-none d-xl-block">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav>
                        @include('layouts.categories')
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
<main>
    @yield('content')
</main>
<footer>
    <div class="btn-up"></div>
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-4 col-xl-4">
                <h3>{{setting('site.help_customer')}}</h3>
                <div class="box__footer-nav">
                    {!! menu('footer_help') !!}
                </div>
            </div>
            <div class="col-12 col-md-4 col-xl-3">
                <h3>{{setting('site.company_footer')}}</h3>
                <div class="box__footer-nav">
                    {!! menu('footer_company') !!}
                </div>
            </div>
            <div class="col-12 col-md-4 col-xl-5">
                <div class="box__footer-link"><a
                        href="tel:8(3852)463620">{{setting('site.CONTACTS_SUPERVISOR_PHONE')}}</a><span>{{setting('site.CONTACTS_SUPERVISOR_PHONE')}}</span>
                </div>
                <div class="box__footer-link"><a
                        href="tel:8(3852)504098">{{setting('site.CONTACTS_MANAGERS_PHONE')}}</a><span>{{setting('site.CONTACTS_MANAGERS_PHONE')}}</span>
                </div>
                <div class="box__footer-info">{{setting('site.ADDRESS_TEXT')}}</div>
                <div class="box__footer-link"><a href="mailto:info@sotka-sem.ru">{{setting('site.CONTACTS_EMAIL')}}</a>
                </div>
            </div>
        </div>
        <div class="row align-center">
            <div class="col-12 col-md-6 col-xl-4">
                <div class="box__copyright">{{setting('site.copyright')}}</div>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <div class="box__link-personal"><a
                        href="{{setting('site.personal_rights')}}">{{setting('site.personal_title_text')}}</a></div>
            </div>
            <div class="col-12 col-md-12 col-xl-5">
                <?php $dev_logo_img = Voyager::setting('site.dev_logo', ''); ?>
                <div class="box__logo-dev"><span>{{setting('site.dev_text')}}</span><a
                        href="{{setting('site.dev_link')}}"><img
                            src="{{ Voyager::image($dev_logo_img) }}" alt=""></a></div>
            </div>
        </div>
    </div>
</footer>

<div class="box__popup-basket" data-popup="basket">
    <div class="wrapper-popup">
        <div class="btn__close">
            <button aria-label="Закрыть попап" data-btn-closepopup><span></span></button>
        </div>
        <div class="wrapper-popup-top">
            <div class="row">
                <div class="col-12">
                    <h2 style="margin-bottom: -0px;border-bottom: 1px solid hsla(0, 0%, 78%, 0.7);">Корзина</h2>
                    <div class="tabs__content-item row">
                        <div class="col-6">
                            <div data-target="orders-tab" class="cart-tab active">Заказы</div>
                        </div>
                        <div class="col-6">
                            <div data-target="preorders-tab" class="cart-tab">Предзаказы</div>
                        </div>
                    </div>

                    <script>
                        $('.cart-tab').on('click', function () {
                            $('.cart-tab').each(function () {
                                $('.cart-tab').removeClass('active');
                                $('#' + $(this).data('target')).removeClass('d-block').addClass('d-none');
                            })
                            $(this).addClass('active');
                            $('#' + $(this).data('target')).toggleClass('d-none d-block');
                        })
                    </script>
                </div>
            </div>
        </div>
        <div class="wrapper-popup-center">
            <div class="tabs-content__data">
                <div id="orders-tab">
                    @if (!empty(session('cart', [])))
                        @include('profile.components.mini-basket')
                    @else
                        <div
                            style="display:flex;align-items:center;justify-content: center;min-height:10rem;color:silver;font-weight:bolder;">
                            Корзина пуста
                        </div>
                    @endif

                    <div class="wrapper-popup-bottom">
                        <div class="row">
                            <div class="col-6">
                                <div class="box__price-title">Итого:</div>
                            </div>
                            <div class="col-6 text-right">
                                <div class="box__price">{{ $miniCartTotal }} <span>₽</span></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="btn"><a href="{{route('profile.orders.cart')}}">В корзину</a></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="preorders-tab" class="d-none">
                    <?php $total = 0 ?>
                    @foreach(\App\Services\Preorder\PreorderService::getCart() as $details)
                        <div class="box__basket-item">
                            <div class="row">
                                <div class="col-3">
                                    <div class="box__image"><a href="#"><img
                                                src="{{ thumbImg($details['image']) }}" alt=""></a></div>
                                </div>
                                <div class="col-9">
                                    <a href="#" class="item_remove remove-from-preorder-cart"
                                       data-id="{{ $details['id'] }}">x</a>
                                    <div class="row">
                                        <div class="col-12"><a href="/preorders/product/{{$details['id']}}">
                                                <h3>{{ $details['name'] }}</h3>
                                            </a>
                                        </div>
                                        <div class="col-5">
                                            <div class="box__quality">
                                                <div class="box__quality-value"><input type="number" data-number="0"
                                                                                       step="{{ $details['multiplicity'] }}"
                                                                                       min="{{ $details['multiplicity'] }}"
                                                                                       name="quantity[]"
                                                                                       class="quantityUpdate{{ $details['id'] }}"
                                                                                       value="{{$details['quantity']}}"
                                                                                       data-id="{{ $details['id'] }}">
                                                </div>
                                                <span class="btn__quality-nav">
                                                    <span class="btn__quality-minus update-cart"
                                                          data-id="{{ $details['id'] }}"
                                                          data-prev-quality>-</span>
                                                    <span class="btn__quality-plus update-cart"
                                                          data-id="{{ $details['id'] }}"
                                                          data-next-quality>+</span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-7">
                                            <div
                                                class="box__price"> {{ $details['price'] * $details['quantity'] }}
                                                <span>₽</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <?php
                            $total += $details['price'] * $details['quantity'];
                            ?>
                    @endforeach

                    <input type="number" value="{{count(\App\Services\Preorder\PreorderService::getCart())}}" id="preOrderBasketCount"
                           disabled hidden>


                    <script>
                        $('.remove-from-preorder-cart').on('click', function () {
                            let id = $(this).data('id'),
                                self = $(this);
                            $.ajax({
                                url: '/preorders/product/' + id + '/remove',
                                method: 'GET',
                                success: function (data) {
                                    $.get('/profile', function (result) {
                                        $('#preorders-tab').html($(result).find('#preorders-tab').html())
                                    })
                                }
                            })
                        })
                    </script>


                    <div class="wrapper-popup-bottom">
                        <div class="row">
                            <div class="col-6">
                                <div class="box__price-title">Итого:</div>
                            </div>
                            <div class="col-6 text-right">
                                <div class="box__price">{{ $total ?? 0 }} <span>₽</span></div>
                            </div>
                            @if($total < ($preorder_minimal ?? 0))
                                <div class="col-8">
                                    <div class="box__price-title">
                                        Минимальная сумма:
                                    </div>
                                </div>
                                <div class="col-4 text-right">
                                    <b>{{$preorder_minimal ?? 0}} <span>₽</span></b>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-12">
                                @if ($total >= ($preorder_minimal ?? 0) && $total != 0)
                                    <div class="btn"><a href="/preorders/cart">В корзину</a></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box__popup-filter" data-popup="filter"></div>

<div class="box__popup-popup" data-popup="navigation">
    <div class="wrapper-popup">
        <div class="btn__close">
            <button aria-label="Закрыть попап" data-btn-closepopup><span></span></button>
        </div>
        <div class="wrapper-popupfilter-top">
            <div class="box__search">
                <div class="box__form">
                    <form action="{{ route('searchProducts') }}" method="GET" role="search">
                        <div class="box__input"><input type="text" name="products" class="searchTitle"
                                                       placeholder="Найти товар"></div>
                        <div class="btn__search">
                            <button><span></span></button>
                        </div>
                        <div id="productData">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="wrapper-popupfilter-center">
            <div class="box__popupfilter-tabs">
                <ul>
                    <li class="active" data-popupfilter="menu">{{setting('site.menu_text')}}</li>
                    <li data-popupfilter="category">{{setting('site.cats_text')}}</li>
                </ul>
            </div>
            <div class="box__popupfilter-tabcontent active" data-popupfilter="menu">
                <div class="box__popupfilter-string">
                    {{ menu('header', 'layouts.headerMenu') }}
                </div>
                <div class="dropdown-item">
                    <a href="{{ route('profile.index') }}" style="color: #6dac52;text-decoration: none;">
                        Личный кабинет
                    </a>
                </div>
                <div>
                    <a class="dropdown-item" style="color: red;" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        Выйти
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
            <div class="box__popupfilter-tabcontent" data-popupfilter="category">
                <div class="box__popupfilter-string">
                    @include('layouts.categories')
                </div>
            </div>
        </div>
    </div>
</div>
@auth
    @if (auth()->user()->active == 'off')
        <div class="box__popup" data-popup='manager' aria-hidden="false" role="dialog">
            <div class="wrapper-popup">
                <div class="btn__close">
                    <button aria-label="Закрыть попап" data-btn-closepopup=""><span></span></button>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h2 class="text-center">{{setting('site.stat_acc')}}</h2>
                        {{--                        <div class="box__description">{{setting('site.stat_acc_text')}}--}}
                        {{--                        </div>--}}
                        <div class="box__description">Вы успешно прошли регистрацию на сайте, но ваш аккаунт пока не
                            активирован администратором. Вы можете дождаться активации, либо позвонить вашему менеджеру.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    @endif
@endauth
@if (!empty(session()->get('cart')) || !empty(\App\Services\Preorder\PreorderService::getCart()))
    @include('scripts.basket')
@endif

@include('scripts.ajax')
@yield('script')


<script>
    window.onload = () => {
        $(function () {
            $('.box__header-center nav li').hover(function () {
                let width = $(this).find('.dropdown-categories').width(),
                    position = $(this).offset(),
                    userScreen = document.documentElement.clientWidth;

                if (width + position.left > userScreen) {
                    $(this).children('.dropdown-categories').css('left', userScreen - (width + position.left) - 60)
                }
            }, function () {
            })
        })
    }
</script>

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (m, e, t, r, i, k, a) {
        m[i] = m[i] || function () {
            (m[i].a = m[i].a || []).push(arguments)
        };
        m[i].l = 1 * new Date();
        k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
    })
    (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

    ym(86828023, "init", {
        clickmap: true,
        trackLinks: true,
        accurateTrackBounce: true
    });
</script>
<noscript>
    <div><img src="https://mc.yandex.ru/watch/86828023" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript>
<!-- /Yandex.Metrika counter -->

@if (!\Illuminate\Support\Facades\Auth::check())
    @include('auth.popup')
@endif

<style>
    .box__personalarea .dropdown-menu {
        display: none;
        /* background-color: #fff; */
        text-align: right;
        position: absolute;
        top: 56%;
        width: 100%;
        /* box-shadow: 0px 5px 6px 0px #39393942; */
        height: 30px;
        z-index: 9999;
        padding: 0 10px;
    }

    .box__personalarea .dropdown-menu a {
        color: #a70000;
    }

    .box__personalarea:hover .dropdown-menu {
        display: block;
    }
</style>

<script>
    $(function () {
        let offsetY = $('.box__header-center').offset().top,
            currentScroll = 0,
            headerHeight = $('.box__header-center').height();

        $('header').height($('header').height());

        $(window).scroll(function () {
            if ($(window).width() > 768) {
                let st = $(this).scrollTop();

                if (st === 0) {
                    $('.box__header-top').css({
                        'position': 'relative',
                        'top': 0,
                        'width': '100%',
                        'background-color': '#f5f7f8',
                        height: headerHeight + 30
                    });
                    $('.box__header-center').css({'position': 'relative', 'top': 0, transition: 'none'});
                } else {
                    if ($(window).scrollTop() >= offsetY)
                        if (st < currentScroll) {
                            $('.box__header-top').css({
                                'position': 'fixed',
                                'top': 0,
                                'width': '100%',
                                'background-color': '#f5f7f8',
                                height: headerHeight + 30,
                            });
                            $('.box__header-center').css({
                                'position': 'fixed',
                                'top': headerHeight + 30,
                                'width': '100%',
                                transition: 'top .3s ease'
                            });
                        } else {
                            $('.box__header-top').css({
                                'background-color': '#f5f7f8',
                                top: (headerHeight + 30) * -1
                            });
                            if ($(window).scrollTop() >= offsetY)
                                $('.box__header-center').css({'position': 'fixed', 'top': 0, 'width': '100%'});
                            else
                                $('.box__header-center').css('position', 'relative');
                        }
                }

                currentScroll = $(this).scrollTop();
            }
        })

        @if (isset($_GET['popup']))
        $('[data-popup=authorization]').iziModal('open')
        @endif
    })
</script>

@stack('scripts')

</body>
</html>

@auth
    @php
        $old = DB::table('cart')->where('user_id', Auth::id())->first();

        if (!is_null($old))
            if ((!session()->exists('cart') && !is_null($old) && !empty(json_decode($old->json))) || ($old->json != json_encode(session()->get('cart')))) {
                session()->put('cart', json_decode($old->json, true));
                session()->put('cart_date', time());

                header("Refresh:0");
            }
    @endphp
@endauth
