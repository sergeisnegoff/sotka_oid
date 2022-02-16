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

            <div id="productFind">
                <div id="productData">
                    <?php $count = 0; ?>
                    @if(\Request::is('products'))
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

                                            @foreach ($seeds as $seed)
                                                @if($seed->category_id == $cat->id)
                                            <div class="swiper-slide">
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
                                                    <?php $count++ ?>
                                                @endif
                                            @endforeach


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
                                <?php $count++ ?>
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
                })
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


