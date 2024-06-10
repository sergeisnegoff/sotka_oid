<?php
$endDate = \Carbon\Carbon::parse($preorder->end_date);
$now = \Carbon\Carbon::now();
$showBuyButton = !$endDate->isSameDay($now);
$diff = $endDate->diff($now);
?>


@extends('layouts.app')
@section('content')
    <div class="box__breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul>
                        <li><a href="/">{{setting('site.main_title_buttom')}}</a></li>
                        <li><a href="/preorders">Предзаказы</a></li>
                        <li>{{ $preorder->title }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <section class="box__product-catalog">
        <div class="container">
            <div style="margin-bottom: 20px;">
                <div style="justify-content:space-between;align-items: end" class="d-flex row">

                    <div class="col-12 col-lg-6">
                    <p style="font-size:28px;font-weight:bolder;margin-bottom:0;"><b>{{ $preorder->title }}</b></p>
                    </div>
                    <div class="col-12 col-lg-6">
                    <p style="margin-bottom:0;"><b>Предзаказ закончится через {{ $diff->d }} дней, {{ $diff->h }}
                            часов, {{ $diff->i }} минут</b></p>
                    </div>
                </div>
                <p style="font-size:18px;margin-bottom:0;font-weight:bolder;">Минимальная сумма
                    заказа {{ number_format($preorder->min_order, 0, 2, ' ') }} рублей</p>
                <p style="font-size:18px;margin-bottom:0;font-weight:bolder;">Предварительная оплата составляет
                    {{ $preorder->prepay_percent }}% от суммы заказа</p>
                <p style="font-size:18px;margin-bottom:0; font-weight:bolder;">С информацией по данному заказу можно
                    ознакомиться на
                    <a href="/preorders/info/{{$preorder->id}}/" style="display:inline;" class="btn">
                            <button>
                                странице заказа
                            </button>
                    </a>
                </p>
                <p  style="font-size:18px;margin-bottom:0; font-weight:bolder;">
                   Сумма по данному предзаказу в Вашей корзине составляет <span id="concrete-preorder-price">{{\App\Services\TotalsService::getUserTotalByPreorder($preorder)}}</span> ₽
                </p>
                <p></p>

            </div>
            <style>
                @media all and (min-width:1200px) {
                    .product-preorder {
                        /*max-width:20%;*/
                    }
                }
                @media all and (min-width: 768px) and (max-width: 1440px) {
                    .product-preorder:nth-child(n+4) {
                        display:none;
                    }
                }
                @media all and (max-width: 768px) {
                    .product-preorder:nth-child(n+3) {
                        display:none;
                    }
                }
            </style>
            <div id="productFind">
                @include('preorder.components.category-tabs')
                <div id="productData">
                    @foreach ($currentCategory->childs as $cat)
                        @if ($cat->products()->limit(4)->count() > 0)
                            <div class="row" style="margin-top:15px;" data-catalog data-catalog-grid>
                                <div class="col-12">
                            <a href="/preorders/category/{{$cat->id}}/products"><p style="color:black; font-size: 28px;font-weight: bolder;">{{ $cat->title }}</p></a>
                                </div>

                                @foreach ($cat->products()->limit(4)->get() as $seed)
                                    @if($seed->hard_limit === 0) @continue @endif
                                    <div class="col-6 col-md-4 col-xl-2 fadeIn product-preorder">
                                        <div class="box__product-item">
                                            <div class="wrapper-img" style="position: relative;">
                                                <div class="box__image"
                                                     style="width: 100%;height: 100%;position: relative;">
                                                    <div class="swiper gallery-product-card"
                                                         style="height: 100%;">
                                                        <div class="swiper-wrapper">
                                                            <div class="swiper-slide">
                                                                <a class="aslide"
                                                                   href="/preorders/product/{{$seed->id}}">
                                                                    <span class="imgslide lazy"
                                                                          data-bg="{{ $seed->image ? asset('storage/'.$seed->image) :  asset('/storage/'.$preorder->default_image) }}">
                                                                        <div
                                                                            class="swiper-lazy-preloader"></div>
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="wrapper-info">
                                                <div class="box__category">
                                                    <a href="/preorders/category/{{ $cat->id }}">{{ $cat->title }}</a>
                                                </div>
                                                <div class="box__title">
                                                    <a href="/preorders/product/{{$seed->id}}">
                                                        <h3>{{$seed->title}}</h3>
                                                    </a>
                                                </div>
                                            </div>
                                            @guest
                                                @if ($showBuyButton)
                                                    <div class="wrapper-button">
                                                        <div class="btn"><a href="javascript:;" data-btn-popup="authorization">
                                                                Купить</a></div>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="wrapper-button wrapper-button-auth">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="box__product-price">
                                                                <span
                                                                    class="box__price-sale">{{number_format($seed->price, 2, ',', ' ')}} ₽</span>
                                                            </div>
                                                        </div>
                                                        @if($showBuyButton)
                                                            <div class="col-6">
                                                                <div class="box__quality">
                                                                    <div class="box__quality-value">

                                                                        <input

                                                                            type="number"
                                                                            name="quantity"
                                                                            class="quantity{{$seed->id}}"
                                                                            data-number="{{ $seed->multiplicity }}"
                                                                            step="{{ $seed->multiplicity }}"
                                                                            min="{{ $seed->multiplicity }}"
                                                                            max="{{ $seed->total }}"
                                                                            value="{{ $seed->multiplicity }}">
                                                                    </div>
                                                                    <span class="btn__quality-nav">
                                                                        <span class="btn__quality-minus update-cart"
                                                                              data-id="{{$seed->id}}"
                                                                              data-prev-quality>-</span>
                                                                        <span class="btn__quality-plus update-cart"
                                                                              data-id="{{$seed->id}}" data-next-quality>+</span>
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div class="col-12">
                                                                <div class="btn d-flex">
                                                                    <a
                                                                        class="add-to-cart-preorder {{ $cartKeys->contains($seed->id) ? 'ifcart' : '' }}"
                                                                        style="color: white; margin-right: 20px; {{ $cartKeys->contains($seed->id) ? "background: #A16C21" : '' }}"
                                                                        value="{{ $seed->id }}">{{ $cartKeys->contains($seed->id) ? 'Докупить' : 'Купить' }}
                                                                    </a>
                                                                    <div class="ifcart">
                                                                        @if($cartKeys->contains($seed->id))
                                                                            Товар есть в корзине
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endguest
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-6 col-lg-4 col-md-4 col-xl-2 fadeIn" style=" padding: 0 15px;">
                                    <div class="box__product-item">
                                        <div class="wrapper" style="position: relative; min-height: 100%">
                                            <div class="btn"><a
                                                    href="/preorders/category/{{ $cat->id }}/products">Посмотреть
                                                    все</a></div>
                                            <div class="btn btn-white"><a href="{{ route('home') }}">На главную</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @include('filter')
    @include('scripts.filter')

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


