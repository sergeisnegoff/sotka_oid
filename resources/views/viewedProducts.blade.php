<section class="box__productsviewed">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2>{{setting('site.viewed_products')}}</h2>
                <div class="wrapper__nav-productsviewed">
                    <div class="slider-productsviewed-prev123"></div>
                    <div class="slider-productsviewed-next123"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="box__slider-productsviewed123">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                    @foreach($seedsViewed as $sv)
                        <div class="swiper-slide">
                            <div class="col-12 col-md-12 col-xl-12 fadeIn">
                            <div class="box__product-item">
                            <div class="wrapper-img" style="position: relative;">
                                <div class="box__image" style="width: 100%;height: 100%;position: relative;">
                                    <div class="swiper gallery-product-card" style="height: 100%;">
                                        <div class="swiper-wrapper">
                                            <div class="swiper-slide">
                                                <a class="aslide" href="/product/{{$sv->id}}">
                                                    <span class="imgslide" style="background-image: url( '{{Voyager::image($sv->images)}}' );"></span>
                                                </a>
                                            </div>
                                            @foreach(json_decode($sv->images_gallery) ?? [] as $image)
                                                <div class="swiper-slide">
                                                    <a class="aslide" href="/product/{{$sv->id}}">
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
                                <div class="box__category"><a href="/products/{{$sv->category->title}}">{{$sv->category->title}}</a></div>
                                <div class="box__title"><a href="/product/{{$sv->id}}"><h3>{{$sv->title}}</h3></a></div>
                                <div class="box__description"><p>{{$sv->description}}</p></div>
                            </div>
                            <div class="wrapper-button">
                                <div class="btn"><a href="/product/{{$sv->id}}">Купить</a></div>
                            </div>
                            </div>
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
