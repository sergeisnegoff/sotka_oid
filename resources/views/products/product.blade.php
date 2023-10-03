@extends('layouts.app')
@section('content')
    <div class="box__breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul>
                        <li><a href="/">{{setting('site.main_title_buttom')}}</a></li>
                        <li><a href="/products/{{$cat->title}}/">{{$cat->small_name}}</a></li>
                        <li><a href="/products/{{$seed->category->id}}/{{$seed->category->title}}">{{$seed->category->small_name}}</a></li>
                        <li>{{$seed->title}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <section class="box__product">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>{{$seed->title}}</h1>
                </div>
            </div>
            <div class="box__product-information">
                <div class="row">
                    <div class="col-12">
                        <div class="btn btn-white"><a href="#"
                                                      onclick="window.history.go(-1); return false;">{{setting('site.back_button')}}</a>
                        </div>
                    </div>
                </div>
                <div class="row">


                    @if (!empty( Voyager::image($seed->images) ))
                        <div class="col-12 col-md-5 col-lg-4 col-xl-3 col-xxl-4">
                            <div class="gallery-container">
                                <div class="swiper-container gallery-main">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide">
                                            <div class="box__image">
                                                <a href="{{ Voyager::image($seed->images) }}" data-fancybox="img">
                                                    <img src="{{ Voyager::image( $seed->images ) }}" alt="{{$seed->title}}">
                                                </a>
                                            </div>
                                        </div>

                                        @if (!empty( $seed->images_gallery ))
                                            @foreach(json_decode($seed->images_gallery, true) as $image)
                                                <div class="swiper-slide">
                                                    <div class="box__image">
                                                        <a href="{{ Voyager::image($image) }}" data-fancybox="img">
                                                            <img src="{{ Voyager::image($image) }}" alt="{{$seed->title}}">
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    @if (!empty( $seed->images_gallery ))
                                        <div class="swiper-button-prev"></div>
                                        <div class="swiper-button-next"></div>
                                    @endif
                                </div>
                                @if (!empty( $seed->images_gallery ))
                                    <div class="swiper-container gallery-thumbs">
                                        <div class="swiper-wrapper">
                                            <div class="swiper-slide">
                                                <div class="box__image_thumb">
                                                    <img src="{{ Voyager::image( $seed->images ) }}" alt="{{$seed->title}}">
                                                </div>
                                            </div>
                                            @foreach(json_decode($seed->images_gallery, true) as $image)
                                                <div class="swiper-slide">
                                                    <div class="box__image_thumb">
                                                        <img src="{{ Voyager::image($image) }}" alt="{{$seed->title}}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    <div class="col-12 col-md-7 col-lg-8 col-xl-9 offset-xxl-1 col-xxl-7">
                        <div class="box__tabs">
                            <ul>
                            <!--                                <li data-tab="characteristics" class="active">{{setting('site.specifications_product')}}</li>-->
                                <li data-tab="description">{{setting('site.description_product')}}</li>
                                @if ($seed->video_link)
                                    <li data-tab="video">{{setting('site.video_product')}}</li>
                                @endif
                            </ul>
                        </div>
                    <!--                        <div data-tab="characteristics" class="box__tab-content active" style="min-height: 120px">
                            <div class="row">
                                <div class="col-12">
                                    <div class="box__product-characteristics">
                                        <ul>
                                            @foreach($seed->subSpecification as $s)
                        <li><span>{{ \App\Models\Specification::find($s->specification)->title }}:</span> {{$s->title}}</li>
                                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>-->
                        <div data-tab="description" class="box__tab-content active" style="min-height: 120px">
                            <div class="box__product-description">
                                <p>{{$seed->description }}</p>
                            </div>
                        </div>
                        <div data-tab="video" class="box__tab-content" style="min-height: 120px">
                            <div class="row">
                                <?php
                                if(!empty($seed->video_link)) {
                                $video = explode('=', $seed->video_link);?>
                                <div class="col-12 col-md-6">
                                    <iframe width="100%" height="300"
                                            src="https://www.youtube-nocookie.com/embed/{{$video[1]}}?controls=0"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen></iframe>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        @if (\Illuminate\Support\Facades\Auth::check() && auth()->user()->active != 'off')
                            <div class="wrapper__product-bottom">
                                <div class="box__product-price">
                                    @if(!empty($seed->new_price))
                                        <span class="box__price-normal">{{$seed->price}} ₽</span>
                                        <span class="box__price-sale">{{$seed->new_price}} ₽</span>
                                    @else
                                        <span class="box__price-normal">{{$seed->price}} ₽</span>
                                    @endif
                                </div>
                                <div class="box__product-status">в наличии</div>
                                <div class="box__product-quality">
                                    <div class="box__quality">
                                        <div class="box__quality-value"><input type="number" name="quantity"
                                                                               class="quantity{{ $seed->id }}"
                                                                               data-number="{{ $seed->multiplicity }}"
                                                                               step="{{ $seed->multiplicity }}"
                                                                               min="{{ $seed->multiplicity }}"
                                                                               max="{{ $seed->total }}"
                                                                               value="{{ $seed->multiplicity }}"></div>
                                        {{--                                        @if($seed->multiplicity <= $seed->total)--}}
                                        <span class="btn__quality-nav">
                                                <span class="btn__quality-minus update-cart" data-id="{{$seed->id}}"
                                                      data-prev-quality>-</span>
                                                <span class="btn__quality-plus update-cart" data-id="{{$seed->id}}"
                                                      data-next-quality>+</span>
                                            </span>
                                        {{--                                        @endif--}}
                                    </div>
                                </div>
                                {{--                                @if($seed->multiplicity > $seed->total && (!is_null(session('cart')) && !in_array($seed->id, array_keys(session('cart')))))--}}
                                <div class="btn"><a
                                        class="add-to-cart {{ $cartKeys->contains($seed->id) ? 'ifcart' : '' }}"
                                        value="{{ $seed->id }}">{{ $cartKeys->contains($seed->id) ? 'Докупить' : 'Купить' }}</a>
                                    <div class="ifcart">@if($cartKeys->contains($seed->id))Товар есть в
                                        корзине@endif</div>
                                </div>
                            </div>
                            {{--                            @endif--}}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="box__productsviewed">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>{{setting('site.related_products')}}</h2>
                    <div class="wrapper__nav-productsviewed">
                        <div class="slider-productsviewed-prev"></div>
                        <div class="slider-productsviewed-next"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="box__slider-productsviewed">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                @php($continue = [])
                                @foreach($seeds as $sv)
                                    @if (in_array($sv->id, $continue)) @continue @endif
                                    <div class="swiper-slide">
                                        <div class="col-12 col-md-12 col-xl-12 fadeIn">
                                        <div class="box__product-item">
                                            <div class="wrapper-img">
                                                <div class="box__image">
                                                    <a class="aslide" href="/product/{{$sv->id}}">
                                                        <span class="imgslide" style="background-image: url({{ Voyager::image( $sv->images ) }});"></span>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="wrapper-info">
                                                <div class="box__category"><a
                                                        href="/products/{{$seed->category->parent_id}}/{{$seed->category->title}}">{{$sv->category->title}}</a>
                                                </div>
                                                <div class="box__title"><a href="/product/{{$sv->id}}">
                                                        <h3>{{$sv->title}}</h3></a></div>
                                                <div class="box__description"><p>{{ $sv->text }}</p></div>
                                            </div>
                                            <div class="wrapper-button">
                                                <div class="btn"><a href="/product/{{$sv->id}}">Купить</a></div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    @php($continue[] = $sv->id)
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('viewedProducts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"
          integrity="sha512-yHknP1/AwR+yx26cB1y0cjvQUMvEa2PFzt1c9LlS4pRQ5NOTZFWbhBig+X9G9eYW/8m0/4OXNx8pxJ6z57x0dw=="
          crossorigin="anonymous"/>
{{--    <script>
        $('.box__slider-productsviewed123').slick({
            slidesToShow: 5,
            slidesToScroll: 1,
            arrows: true,
            centerMode: false,
            prevArrow: $('.slider-productsviewed-prev-slick'),
            nextArrow: $('.slider-productsviewed-next-slick'),
        });
    </script>--}}

    <style>
        .slider-productsviewed-prev-slick {
            position: absolute;
            display: block;
            left: 0;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid #DCDCDC;
            transition: opacity ease-out .3s;
            background-image: url(/images/productsviewed-prev.svg?5fa5ebb…);
            background-size: 11px;
            background-position: 8px 7px;
            background-repeat: no-repeat;
            cursor: pointer;
            outline: none;
        }

        .slider-productsviewed-next-slick {
            position: absolute;
            display: block;
            right: 0;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid #DCDCDC;
            transition: opacity ease-out .3s;
            background-image: url(/images/productsviewed-next.svg?1e2760a…);
            background-size: 11px;
            background-position: 11px 7px;
            background-repeat: no-repeat;
            cursor: pointer;
            outline: none;
        }

        .slick-track {
            margin-left: 0;
        }
    </style>
@endsection
