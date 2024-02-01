<?php
$atrProd = '';
$showBuyButton = !(\Carbon\Carbon::parse($category->preorder->end_date))->isSameDay(\Carbon\Carbon::now());
?>


@extends('layouts.app')
@section('content')
    <div class="box__breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul>
                        <li><a href="/">{{setting('site.main_title_buttom')}}</a></li>

                        <li><a href="/preorders/{{ $category->preorder_id }}/category">Предзаказ {{ $category->preorder->title }}</a></li>
                        @if($parentCategory)
                            <li><a href="/preorders/category/{{$parentCategory->id}}/products">{{$parentCategory->title}}</a></li>
                        @endif
                        <li>{{ $category->title }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <section class="box__product-catalog">
        <div class="container">
            <div class="box__product-header">
                <div style="margin-bottom: 32px;" class="row">
                    <div class="col-7" style="font-size: 28px;font-weight: bolder">Предзаказ {{ $category->preorder->title }}</div>
                    <div class="col-2 col-md-9 col-lg-9 col-xl-2">
                    </div>
                    <div class="col-3 col-md-3 col-lg-3 col-xl-3">
                        <div class="box__catalog-view">
                            <ul>
                                <li data-catalog-grid
                                    class="gridAttr <?= $atrProd == 'data-catalog-grid' ? 'active'
                                        : '' ?>  <?= empty($atrProd) ? 'active' : '' ?>">
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
                <div class="row">
                    <div class="col-12" style="font-size: 28px;font-weight: bolder;">{{ $category->title }}</div>
                </div>
            </div>

            <div id="productFind">
                <div id="productData">
                    <div class="row prodAttr"
                             data-catalog <?= !empty($atrProd) ? $atrProd : 'data-catalog-grid'  ?>>

                        @foreach ($products as $seed)
                                <div class="col-6 col-md-4 col-xl-2 fadeIn">
                                    <div class="box__product-item">
                                        <div class="wrapper-img">
                                            <div class="box__image"
                                                 style="width: 100%;height: 100%;position: relative;">
                                                <div class="swiper gallery-product-card" style="height: 100%;">
                                                    <div class="swiper-wrapper">
                                                        <div class="swiper-slide">
                                                            <a class="aslide" href="/preorders/product/{{$seed->id}}">
                                                                <span class="imgslide lazy"
                                                                      data-bg="{{ thumbImg($seed->image ?? $category->preorder->default_image , 220, 346) }}">
                                                                    <div class="swiper-lazy-preloader"></div>
                                                                </span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <!-- If we need pagination -->
                                                    <div class="swiper-pagination"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="wrapper-info">
                                            <div class="box__category"><a
                                                    href="/preorders/category/{{ $category->id }}/products">{{ @$seed->category->title }}</a>
                                            </div>
                                            <div class="box__title"><a href="/preorders/product/{{$seed->id}}">
                                                    <h3> {{$seed->title}} </h3></a>
                                            </div>
                                            <div class="box__description">
                                                <div class="box__characteristics">
                                                    <ul>
                                                    @if(!empty($seed->barcode)) <li>Штрихкод: <b>{{$seed->barcode }}</b></li> @endif
                                                    @if(!empty($seed->container)) <li>Контейнер: <b>{{$seed->container }}</b></li> @endif
                                                    @if(!empty($seed->country)) <li>Страна: <b>{{$seed->country }}</b></li> @endif
                                                    @if(!empty($seed->packaging)) <li>Фасовка: <b>{{$seed->packaging }}</b></li> @endif
                                                    @if(!empty($seed->package_type)) <li>Тип пакета: <b>{{$seed->package_type }}</b></li> @endif
                                                    @if(!empty($seed->weight)) <li>Вес: <b>{{$seed->weight }}</b></li> @endif
                                                    @if(!empty($seed->r_i)) <li>Р.И: <b>{{$seed->r_i }}</b></li> @endif
                                                    @if(!empty($seed->season)) <li>Сезон: <b>{{$seed->season }}</b></li> @endif
                                                    @if(!empty($seed->plant_height)) <li>Высота растения:: <b>{{$seed->plant_height }}</b></li> @endif
                                                    @if(!empty($seed->packaging_type)) <li>Вид упаковки: <b>{{$seed->packaging_type }}</b></li> @endif
                                                    @if(!empty($seed->package_amount)) <li>Количество в упаковке: <b>{{$seed->package_amount }}</b></li> @endif
                                                    @if(!empty($seed->culture_type)) <li>Вид культуры: <b>{{$seed->culture_type }}</b></li> @endif
                                                    @if(!empty($seed->frost_resistance)) <li>Морозостойкость: <b>{{$seed->frost_resistance }}</b></li> @endif
                                                    @if(!empty($seed->additional_1)) <li>Доп. информация: <b>{{$seed->additional_1 }}</b></li> @endif
                                                    @if(!empty($seed->additional_2)) <li>Доп. информация: <b>{{$seed->additional_2 }}</b></li> @endif
                                                    @if(!empty($seed->additional_3 )) <li>Доп. информация: <b>{{$seed->additional_3 }}</b></li> @endif
                                                    @if(!empty($seed->additional_4 )) <li>Доп. информация: <b>{{$seed->additional_4 }}</b></li> @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        @if($showBuyButton)
                                        @switch(true)
                                            @case(!\Illuminate\Support\Facades\Auth::check())
                                                <div class="wrapper-button">
                                                    <div class="btn"><a href="javascript:;" data-btn-popup="authorization">
                                                            Купить</a></div>
                                                </div>
                                            @break
                                            @case(auth()->user()->active == 'off')
                                                <div class="wrapper-button">
                                                    <div class="btn"><a href="javascript:;" data-btn-popup="manager">
                                                            Купить</a></div>
                                                </div>
                                            @break
                                            @default
                                                <div class="wrapper-button wrapper-button-auth">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-6 col-md-6">
                                                                    <div class="box__product-price">
                                                                    <span
                                                                        class="box__price-sale">{{$seed->price}} ₽</span>
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
                                                                    class="add-to-cart-preorder {{ $cartKeys->contains($seed->id) ? 'ifcart' : '' }}"
                                                                    value="{{$seed->id}}">{{ $cartKeys->contains($seed->id) ? 'Докупить' : 'Купить' }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 col-md-6">
                                                            <div class="ifcart">@if($cartKeys->contains($seed->id))Товар
                                                                есть в корзине@endif</div>
                                                        </div>
                                                    </div>
                                                </div>
                                        @endswitch
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function () {
            $('body').on('click', '.box__catalog-view button', function (e) {
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
                $('#productData .swiper').each(function () {
                    this.swiper.update();
                });
                return false;
            }).on('change', '.box__catalog-limit input', function () {
                $(this).closest('.box__catalog-limit').find('.active').removeClass('active');
                $(this).parent().addClass('active');
            }).on('click', '.box__product-item', function (e) {
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


