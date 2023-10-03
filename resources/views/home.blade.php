@extends('layouts.app')
@section('content')
    <main>
        <section class="box__slider-home">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-xl-6">
                        <div class="box__slider-big">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">
                                    @foreach ($bigSlider as $slide)
                                        <div class="swiper-slide">
                                            <div class="wrapper-slide">
                                                <div class="box__cover"><span
                                                        style="background-image: url( '{{ thumbImg( $slide->img, 695, 630) }}' );"></span>
                                                </div>
                                                <div class="wrapper-info">
                                                    <h2>{{ $slide->title }}</h2>
                                                    <div class="box__description">{!! nl2br($slide->text) !!}</div>
                                                    <div class="btn"
                                                         style="text-align: left;position:absolute;bottom:90px;left:45px;">
                                                        @if (!empty($slide->button_text))
                                                            <a href="{{ $slide->button_href ?? $slide->button_new_tab }}"{{ \Illuminate\Support\Str::of($slide->button_href)->afterLast('/')->contains('.') ? 'download' : '' }}
                                                                {{ !empty($slide->button_new_tab) ? 'target="_blank"' : '' }}>{{ $slide->button_text }}</a>
                                                        @endif
                                                        @if (!empty($slide->button2_text))
                                                            <a href="{{ $slide->button2_href ?? $slide->button2_new_tab }}"{{ \Illuminate\Support\Str::of($slide->button2_href)->afterLast('/')->contains('.') ? 'download' : '' }}
                                                                {{ !empty($slide->button2_new_tab) ? 'target=_blank' : '' }}>{{ $slide->button2_text }}</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>


                                <div class="slider-big-next"></div>
                                <div class="slider-big-prev"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="box__slider-mini">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">
                                    @foreach ($sliderBlocks as $chunk)
                                        <div class="swiper-slide">
                                            <div class="row">
                                                @foreach ($chunk as $slide)
                                                    <div class="col-12 col-md-6">
                                                        <div class="box__slider-item">
                                                            <div class="box__cover"><span
                                                                    style="background-image: url( '{{ thumbImg( $slide->img, 332, 250) }}' );"></span>
                                                            </div>
                                                            <h3>{{ $slide->title }}</h3>
                                                            <div class="btn"><a {{ \Illuminate\Support\Str::of($slide->button_href)->afterLast('/')->contains('.') ? 'download' : '' }}
                                                                    href="{{ $slide->button_href ?? $slide->button_new_tab }}" {{ empty($slide->button_href) ? 'target=_blank' : '' }}>{{ $slide->button_text }}</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="box__product-catalog">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2>{{setting('site.card_product')}}</h2>
                    </div>
                </div>
                <div id="productFind">
                    <div id="productData">
                        <div class="row" data-catalog data-catalog-grid>
                            <?php $count = 0; ?>
                            @foreach ($seeds as $seed)
                                <div class="col-6 col-md-4 col-xl-2 fadeIn" style="margin-bottom: 20px;">
                                    <div class="box__product-item">
                                            <div class="wrapper-img" style="position: relative;">
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
                                                    href="/products/{{$seed->category->parent_id}}/{{$seed->category->title}}">{{@$seed->category->title}}</a>
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
                                                <div class="btn"><a href="javascript:;" data-btn-popup="authorization">
                                                        Купить</a></div>
                                            </div>
                                        @elseif (auth()->user()->active == 'off')
                                            <div class="wrapper-button">
                                                <div class="btn"><a href="javascript:;" data-btn-popup="manager">
                                                        Купить</a></div>
                                            </div>
                                        @else
                                            <div class="wrapper-button wrapper-button-auth">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="box__product-price">
                                                            @if(!empty($seed->new_price))
                                                                <span
                                                                    class="box__price-sale">{{$seed->new_price}} ₽</span>
                                                                <span
                                                                    class="box__price-normal">{{$seed->price}} ₽</span>
                                                            @else
                                                                <span class="box__price-sale">{{$seed->price}} ₽</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="box__quality">
                                                            <div class="box__quality-value"><input type="number"
                                                                                                   name="quantity[]"
                                                                                                   class="quantity{{$seed->id}}"
                                                                                                   data-number="{{ $seed->multiplicity }}"
                                                                                                   step="{{ $seed->multiplicity }}"
                                                                                                   min="{{ $seed->multiplicity }}"
                                                                                                   max="{{ $seed->total }}"
                                                                                                   value="{{ $seed->multiplicity }}">
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
                                                    <div class="col-6">
                                                        <div class="btn">
                                                            <button
                                                                class="add-to-cart {{ $cartKeys->contains($seed->id) ? 'ifcart' : '' }}"
                                                                value="{{$seed->id}}">{{ $cartKeys->contains($seed->id) ? 'Докупить' : 'Купить' }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
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
                    </div>
                </div>
                <input type="hidden" name="" value="{{ isset($_GET['page']) ? $_GET['page'] : 1 }}" id="current_page">
            </div>
        </section>

    </main>

@endsection


