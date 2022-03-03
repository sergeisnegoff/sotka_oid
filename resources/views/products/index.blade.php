@extends('layouts.app')
@section('content')
    <section class="box__product-catalog">
        <div class="container">
            @if( \Request::is('searchProducts') )
                <div class="row">
                    <div class="col-12 " style="text-align:center;">
                        <h1 class="center">Вы искали: {{request('products')}}</h1>
                    </div>
                </div>
            @endif
            @if( \Request::routeIs('products_cats') )
                <div class="box__product-header">
                    <div class="row">
                        <div class="col-6 col-md-2 col-lg-2 d-xl-none">
                            <div class="box__catalog-category">
                                <button data-btn-popup="navigation">Категории</button>
                            </div>
                        </div>
                        <div class="col-6 col-md-1 col-lg-1 col-xl-2" style="max-width: 120px;">
                            <div class="box__catalog-filter">
                                <button data-btn-popup="filter">Фильтр</button>
                            </div>
                        </div>
                        <div class="col-12 col-md-2 col-lg-2 col-xl-2" style="align-items: center">
                            <div class="box__catalog-checkproduction">
                                <div class="box__checkbox">
                                    <div class="wrapper-checkbox">
                                        @foreach (App\Subfilter::all() as $subFilter)
                                            @if($subFilter->title == 'ЗОЛОТАЯ СОТКА АЛТАЯ')
                                                <label style="margin: 0;">
                                                    <input class="filterShow filterChecked" id="sotka-sem-checkbox"
                                                           value="{{$subFilter->title}}" type="checkbox">
                                                    <span>
                                                <span class="box__checkbox-icon"></span>
                                                <span
                                                    class="box__checkbox-text">Продукция Золотой Сотки Алтая</span>
                                            </span>
                                                </label>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-7 col-md-4 col-lg-4 col-xl-4">
                            <div class="box__catalog-sorting">
                                <div class="wrapper-sorting-title d-none d-xl-block">Сортировать</div>
                                <div>
                                    <select name="sort" id="sortSelect"
                                            style="width: 100%; border-color: #6dac52; border-radius: 20px;padding: 5px 20px;">
                                        <option selected>По умолчанию</option>
                                        <option value="ASC/title">Название (А - Я)</option>
                                        <option value="DESC/title">Название (Я - А)</option>
                                        <option value="ASC/price">Цена (низкая &gt; высокая)</option>
                                        <option value="DESC/price">Цена (высокая &gt; низкая)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php $atrProd = []; ?>
                        @if(session('dataAttr'))
                            @foreach(session('dataAttr') as $s)
                                <?php $atrProd = $s?>
                            @endforeach
                        @endif
                        <div class="col-5 col-md-5 col-lg-5 col-xl-4">
                            <div class="box__catalog-view">
                                <ul>
                                    <li data-catalog-grid
                                        class="gridAttr <?= $atrProd == 'data-catalog-grid' ? 'active' : '' ?>  <?= empty($atrProd) ? 'active' : '' ?>">
                                        <button class="gridBtn" type="button" value="data-catalog-grid"><span
                                                style="background-image: url({{asset('img/icon/sorting-card.svg')}});"></span>
                                        </button>
                                    </li>
                                    <li data-catalog-list
                                        class="listAttr <?= $atrProd == 'data-catalog-list' ? 'active' : '' ?>">
                                        <button class="listBtn" type="button" value="data-catalog-list"><span
                                                style="background-image: url({{asset('img/icon/sorting-list.svg')}});"></span>
                                        </button>
                                    </li>
                                    <li data-catalog-card
                                        class="cardAttr <?= $atrProd == 'data-catalog-card' ? 'active' : '' ?>">
                                        <button class="cardBtn" type="button" value="data-catalog-card"><span
                                                style="background-image: url({{asset('img/icon/sorting-grid.svg')}});"></span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div id="productFind">
                <div id="productData">
                    @if(\Request::is('products') || Request::routeIs('products_parent_cats'))
                        @foreach($cats as $cat)
                            <div class="row prodAttr"
                                 data-catalog <?= !empty($atrProd) ? $atrProd : 'data-catalog-grid'  ?>>
                                <div class="col-12">
                                    <a href="/products/{{$cat->id}}/{{ $cat->title }}"
                                       style="text-transform: capitalize;"><h2>{{$cat->title}}</h2></a>
                                </div>

                                <div style="width: 100%;overflow: hidden;position: relative;margin-bottom: 20px;">
                                    <div class="swiper mySwiper">
                                        <div class="swiper-wrapper">

                                            @foreach ($cat->product as $seed)
                                            @if($loop->iteration <= 30)
                                            <div class="swiper-slide KOHb">
                                                <div class="col-12 col-md-12 col-xl-12 fadeIn">
                                                    <div class="box__product-item">
                                                        <div class="wrapper-img" style="position: relative;">
                                                            <div class="box__image" style="width: 100%;height: 100%;position: relative;">
                                                                <div class="swiper gallery-product-card" style="height: 100%;">
                                                                    <div class="swiper-wrapper">
                                                                        <div class="swiper-slide">
                                                                            <a class="aslide" href="/product/{{$seed->id}}">
                                                                                <span class="imgslide" style="background-image: url( '{{Voyager::image($seed->images)}}' );">

                                                                                </span>
                                                                            </a>
                                                                        </div>
                                                                        @foreach(json_decode($seed->images_gallery) ?? [] as $image)
                                                                            <div class="swiper-slide">
                                                                                <a class="aslide" href="/product/{{$seed->id}}">
                                                                                    <span class="imgslide" style="background-image: url( '{{ Voyager::image($image) }}' );"></span>
                                                                                </a>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <!-- If we need pagination -->
                                                                    <div class="swiper-pagination"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="wrapper-info">
                                                            <div class="box__category">
                                                                <a href="/products/{{$seed->category->parent_id}}/{{$seed->category->title}}">{{$seed->category->title}}</a>
                                                            </div>
                                                            <div class="box__title"><a href="/product/{{$seed->id}}">
                                                                    <h3> {{$seed->title}} </h3></a>
                                                            </div>
                                                            <div class="box__description">
                                                                <div class="box__characteristics">
                                                                    <ul>
                                                                        <?php $specscount = 0 ?>
                                                                        @foreach($seed->subSpecification as $specs)
                                                                            <li>{{$specs->specification}}:
                                                                                <span>{{$specs->title}}</span></li>
                                                                            <?php $specscount++ ?>
                                                                        @endforeach
                                                                    </ul>
                                                                    @if($specscount >= 7)
                                                                        <div class="box__characteristics-button">
                                                                            <span class="box__characteristics-status">Все характеристики</span>
                                                                            <span class="box__characteristics-status">Скрыть характеристики</span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @guest
                                                            <div class="wrapper-button">
                                                                <div class="btn"><a href="{{route('login')}}"> Купить</a></div>
                                                            </div>
                                                        @else
                                                            <div class="wrapper-button wrapper-button-auth">
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <div class="box__product-price">
                                                                            @if(!empty($seed->new_price))
                                                                                <span class="box__price-sale">{{$seed->new_price}} ₽</span>
                                                                                <span class="box__price-normal">{{$seed->price}} ₽</span>
                                                                            @else
                                                                                <span
                                                                                    class="box__price-sale">{{$seed->price}} ₽</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="box__quality">
                                                                            <div class="box__quality-value"><input type="number"
                                                                                                                   name="quantity"
                                                                                                                   class="quantity{{$seed->id}}"
                                                                                                                   data-number="0"
                                                                                                                   step="{{ $seed->multiplicity }}"
                                                                                                                   min="1"
                                                                                                                   max="100"
                                                                                                                   value="1">
                                                                            </div>
                                                                            @if ($seed->multiplicity <= $seed->total)
                                                                                <span class="btn__quality-nav">
                                                                            <span class="btn__quality-minus update-cart"
                                                                                  data-id="{{$seed->id}}"
                                                                                  data-prev-quality>-</span>
                                                                <span class="btn__quality-plus update-cart"
                                                                      data-id="{{$seed->id}}" data-next-quality>+</span>
                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="btn">
                                                                            <button class="add-to-cart" value="{{$seed->id}}">
                                                                                Купить
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endguest
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            @endforeach
                                            <div class="swiper-slide">
                                                <div class="col-12 col-md-12 col-xl-12 fadeIn">
                                                    <div class="box__product-item">
                                                        <div class="wrapper" style="position: relative;">
                                                            <div class="btn"><a href="/products/{{ @$seed->category->parent_id }}/{{ @$seed->category->title }}">Посмотреть все</a></div>
                                                            <div class="btn btn-white"><a href="{{ route('home') }}">На главную</a></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                        <div class="swiper-pagination" style="position: relative;bottom: 0;"></div>
                                        <div class="slider-product-next"></div>
                                        <div class="slider-product-prev"></div>
                                        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
                                </div>


                            </div>
                        @endforeach
                    @else
                        <div class="row prodAttr"
                             data-catalog <?= !empty($atrProd) ? $atrProd : 'data-catalog-grid'  ?>>
                            @foreach ($seeds as $seed)
                                <div class="col-6 col-md-4 col-xl-2 fadeIn">
                                    <div class="box__product-item">
                                        <div class="wrapper-img">
                                            <div class="box__image" style="width: 100%;height: 100%;position: relative;">
                                                <div class="swiper gallery-product-card" style="height: 100%;">
                                                    <div class="swiper-wrapper">
                                                        <div class="swiper-slide">
                                                            <a class="aslide" href="/product/{{$seed->id}}">
                                                                <span class="imgslide" style="background-image: url( '{{Voyager::image($seed->images)}}' );"></span>
                                                            </a>
                                                        </div>
                                                        @foreach(json_decode($seed->images_gallery) ?? [] as $image)
                                                            <div class="swiper-slide">
                                                                <a class="aslide" href="/product/{{$seed->id}}">
                                                                    <span class="imgslide" style="background-image: url( '{{ Voyager::image($image) }}' );"></span>
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <!-- If we need pagination -->
                                                    <div class="swiper-pagination"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="wrapper-info">
                                            <div class="box__category"><a
                                                    href="/products/{{ @$seed->category->parent_id }}/{{ @$seed->category->title }}">{{ @$seed->category->title }}</a>
                                            </div>
                                            <div class="box__title"><a href="/product/{{$seed->id}}">
                                                    <h3> {{$seed->title}} </h3></a>
                                            </div>
                                            <div class="box__description">
                                                <div class="box__characteristics">
                                                    <ul>
                                                        <?php $specscount = 0 ?>
                                                        @foreach($seed->subSpecification as $specs)
                                                            @php($specification = \App\Specification::find($specs->specification))
                                                            <li>{{$specification->title}}:
                                                                <span>{{$specs->title}}</span></li>
                                                            <?php $specscount++ ?>
                                                        @endforeach
                                                    </ul>
                                                    @if($specscount >= 7)
                                                        <div class="box__characteristics-button">
                                                            <span
                                                                class="box__characteristics-status">Все характеристики</span>
                                                            <span class="box__characteristics-status">Скрыть характеристики</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @if (!\Illuminate\Support\Facades\Auth::check())
                                            <div class="wrapper-button">
                                                <div class="btn"><a href="javascript:;" data-btn-popup="authorization"> Купить</a></div>
                                            </div>
                                        @elseif (auth()->user()->active == 'off')
                                            <div class="wrapper-button">
                                                <div class="btn"><a href="javascript:;" data-btn-popup="manager"> Купить</a></div>
                                            </div>
                                        @else
                                            <div class="wrapper-button wrapper-button-auth">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-6 col-md-6">
                                                                <div class="box__product-price">
                                                                    @if(!empty($seed->new_price))
                                                                        <span class="box__price-sale">{{$seed->new_price}} ₽</span>
                                                                        <span class="box__price-normal">{{$seed->price}} ₽</span>
                                                                    @else
                                                                        <span
                                                                            class="box__price-sale">{{$seed->price}} ₽</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-6 col-md-6">
                                                                <div class="box__quality" style="margin-left: auto;">
                                                                    <div class="box__quality-value"><input type="number"
                                                                                                           name="quantity"
                                                                                                           class="quantity{{$seed->id}}"
                                                                                                           data-number="{{ $seed->multiplicity }}"
                                                                                                           step="{{ $seed->multiplicity }}"
                                                                                                           min="{{ $seed->multiplicity }}"
                                                                                                           max="{{ $seed->total }}"
                                                                                                           value="{{ $seed->multiplicity }}">
                                                                    </div>
{{--                                                                    @if ($seed->multiplicity <= $seed->total)--}}
                                                                        <span class="btn__quality-nav">
                                                                                    <span
                                                                                        class="btn__quality-minus update-cart"
                                                                                        data-id="{{$seed->id}}"
                                                                                        data-prev-quality>-</span>
                                                                        <span class="btn__quality-plus update-cart"
                                                                              data-id="{{$seed->id}}" data-next-quality>+</span>

                                                                        </span>
{{--                                                                    @endif--}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-6">
                                                        <div class="btn" style="text-align: left;">
                                                            <button
                                                                class="add-to-cart {{ $cartKeys->contains($seed->id) ? 'ifcart' : '' }}"
                                                                value="{{$seed->id}}">{{ $cartKeys->contains($seed->id) ? 'Докупить' : 'Купить' }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-6">
                                                        <div class="ifcart">@if($cartKeys->contains($seed->id))Товар есть в корзине@endif</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endguest
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @include('filter')
    @include('scripts.filter')
    <script>
        $(document).ready(function () {
            var height = $('.mySwiper .box__product-item:last-child').height();
            $('.box__product-item .wrapper').height(height);

            $("body").on('click', '.add-to-cart-prod', function (e) {
                e.preventDefault();
                let but = $(this).val();
                $.ajax({
                    url: '{{ url('add-to-cart/') }}/' + but,
                    method: "post",
                    data: {quantity: $(".quantity").val()},
                    success: function (response) {
                        window.location.reload();
                    }
                });
            }).on('click', '.box__catalog-view button', function (e) {
                e.preventDefault();
                let _self = $(this);

                $(this).parent().addClass('active');
                $(this).parent().siblings('.active').removeClass('active')
                $.each($('.prodAttr')[0].attributes, function (item) {
                    if (/catalog$/.test($('.prodAttr')[0].attributes[item].name) === false && $('.prodAttr')[0].attributes[item].name.includes('data')) {
                        $('.prodAttr').removeAttr($('.prodAttr')[0].attributes[item].name);
                        $('.prodAttr').attr(_self.attr('value'), true);
                    }
                });
                $('#productData .swiper').each(function (){
                    this.swiper.update();
                });
                return false;
            }).on('change', '.box__catalog-limit input', function () {
                $(this).closest('.box__catalog-limit').find('.active').removeClass('active');
                $(this).parent().addClass('active');
            }).on('click', '.box__product-item', function (e) {
            })

            if (localStorage.getItem('sotka-sem-checkbox') === 'true') {
                $('#sotka-sem-checkbox').attr('checked', true)
                $.ajax({
                    data: {subFilter: ['ЗОЛОТАЯ СОТКА АЛТАЯ']},
                    success:
                        function (data) {
                            data = $(data).find('div#productData');
                            $('#productFind').html(data);
                        }
                });
            }
            $('#sotka-sem-checkbox').change(function () {
                if ($(this).is(':checked')) {
                    localStorage.setItem('sotka-sem-checkbox', 'true');
                } else
                    localStorage.setItem('sotka-sem-checkbox', 'false');
            })

        });
    </script>

    <style>
        .box__catalog-limit label {
            transition: .3s;
        }

        .box__catalog-limit label:hover {
            color: #6DAC52;
            cursor: pointer;
        }
    </style>
@endsection


