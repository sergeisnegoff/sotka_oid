<section class="box__productsviewed">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2>{{setting('site.viewed_products')}}</h2>
                <div class="wrapper__nav-productsviewed">
                    <div class="slider-productsviewed-prev-slick"></div>
                    <div class="slider-productsviewed-next-slick"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="box__slider-productsviewed123">
                    @foreach($seedsViewed as $sv)
                        <div class="box__product-item" style="margin-left: 10px; margin-right: 10px">
                            <div class="wrapper-img">
                                <div class="box__image"><a href="/product/{{$sv->id}}"><span style="background-image: url({{ Voyager::image( $sv->images ) }});"></span></a></div>
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
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
