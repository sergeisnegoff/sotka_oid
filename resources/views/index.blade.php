@extends('layouts.app')
@section('content')
    <section class="<?= \Request::is('searchProducts') ? 'box__search-page' : 'box__product-catalog'  ?> " >
        <div class="container">
            @if( \Request::is('searchProducts') )
            <div class="row">
                <div class="col-12 " style="text-align:center;">
                    <h1 class="center">Вы искали: {{request('products')}}</h1>
                </div>
            </div>
            @endif
            <div class="box__product-header">
                <div class="row">
                    <div class="col-6 col-md-2 col-lg-2 d-xl-none">
                        <div class="box__catalog-category">
                            <button data-btn-popup="navigation">Категории</button>
                        </div>
                    </div>
                    <div class="col-6 col-md-2 col-lg-1 col-xl-1">
                        <div class="box__catalog-filter">
                            <button data-btn-popup="filter">Фильтр</button>
                        </div>
                    </div>
                    <div class="col-12 col-md-2 col-lg-2 col-xl-2">
                        <div class="box__catalog-checkproduction">
                            <div class="box__checkbox">
                                <div class="wrapper-checkbox">
                                    @foreach (\App\Models\subFilter::all() as $subFilter)
                                        @if($subFilter->title == 'Золотая сотка алтая')
                                    <label>
                                        <input class="filterShow filterChecked" value="{{$subFilter->title}}" type="checkbox">
                                        <span>
                                <span class="box__checkbox-icon"></span>
                                <span class="box__checkbox-text">Продукция Золотой Сотки Алтая</span>
                            </span>
                                    </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-7 col-md-3 col-lg-4 col-xl-5">
                        <div class="box__catalog-sorting">
                            <div class="wrapper-sorting-title d-none d-xl-block">Сортировать</div>
                            <div>
                                <select name="sort" id="sortSelect">
                                    <option selected>По умолчанию</option>
                                    <option value="DESC/title">Название (А - Я)</option>
                                    <option value="ASC/title">Название (Я - А)</option>
                                    <option value="ASC/price">Цена (низкая &gt; высокая)</option>
                                    <option value="DESC/price">Цена (высокая &gt; низкая)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-2 col-md-2 col-lg-2 col-xl-3">
                        <div class="box__catalog-limit">
                            <div class="wrapper__cataloglimit-active d-lg-none">20</div>
                            <ul>
                            <div  data-toggle="buttons">
                                    <li> <label class="btn btn-secondary active">
                                            <input type="radio" name="radioShow" class="radioShow" value="20" autocomplete="off" checked> 20
                                        </label></li>
                                    <li><label class="btn btn-secondary">
                                            <input type="radio" name="radioShow" class="radioShow" value="40" autocomplete="off"> 40
                                        </label></li>
                                    <li>
                                        <label class="btn btn-secondary">
                                            <input type="radio" name="radioShow" class="radioShow" value="60" autocomplete="off"> 60
                                        </label></li>
                                    <li>
                                        <label class="btn btn-secondary">
                                            <input type="radio" name="radioShow" class="radioShow" value="all" autocomplete="off"> Показать всё
                                        </label>
                                    </li>
                            </div>
                            </ul>
                        </div>
                    </div>
                    <?php $atrProd = []; ?>
                    @if(session('dataAttr'))
                        @foreach(session('dataAttr') as $s)
                            <?php $atrProd = $s?>
                        @endforeach
                    @endif
                    <div class="col-3 col-md-1 col-lg-1 col-xl-1">
                        <div class="box__catalog-view">
                            <ul>
                                <li data-catalog-grid class="gridAttr <?= $atrProd == 'data-catalog-grid' ? 'active' : '' ?>  <?= empty($atrProd) ? 'active' : '' ?>">
                                    <button class="gridBtn" value="data-catalog-grid"><span style="background-image: url({{asset('img/icon/sorting-card.svg')}});"></span>
                                    </button>
                                </li>
                                <li data-catalog-list class="listAttr <?= $atrProd == 'data-catalog-list' ? 'active' : '' ?>">
                                    <button class="listBtn" value="data-catalog-list"><span style="background-image: url({{asset('img/icon/sorting-list.svg')}});"></span>
                                    </button>
                                </li>
                                <li data-catalog-card class="cardAttr <?= $atrProd == 'data-catalog-card' ? 'active' : '' ?>">
                                    <button class="cardBtn" value="data-catalog-card"><span style="background-image: url({{asset('img/icon/sorting-grid.svg')}});"></span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
               <div id="productFind">
                <div id="productData">
                    @if(\Request::is('products'))
                    @foreach(\App\Models\Category::all() as $cat)
                        @if( count($cat->product) > 0 && (\Request::is('products'))  )
                            <div class="row">
                                <div class="col-12">
                                    <h2>{{$cat->title}}</h2>
                                </div>
                            </div>
                        @endif
                        <div class="row prodAttr" data-catalog  <?= !empty($atrProd) ? $atrProd : 'data-catalog-grid'  ?>>
                            <?php $count = 0; ?>
                            @foreach ($seeds as $seed)
                                @if($seed->category_id == $cat->id)
                                    <div class="col-6 col-md-4 col-xl-2 fadeIn">
                                        <div class="box__product-item">
                                            <div class="wrapper-img">
                                                <div class="box__image"><a href="/product/{{$seed->id}}"><span
                                                            style="background-image: url( '{{Voyager::image($seed->images)}}' );"></span></a>
                                                </div>
                                            </div>
                                            <div class="wrapper-info">
                                                <div class="box__category"><a
                                                        href="/product/{{$seed->id}}">{{$seed->category->title}}</a>
                                                </div>
                                                <div class="box__title"><a href="/product/{{$seed->id}}">
                                                        <h3> {{$seed->title}} </h3></a>
                                                </div>
                                                <div class="box__description">
                                                    <div class="box__characteristics">
                                                        <ul>
                                                            <?php $specscount=0 ?>
                                                            @foreach($seed->subSpecification as $specs)
                                                            <li>{{$specs->specification}}: <span>{{$specs->title}}</span></li>
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
                                                                    <span class="box__price-sale">{{$seed->price}} ₽</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="box__quality">
                                                                <div class="box__quality-value"><input type="number" class="quantity{{$seed->id}}" name="quantity[]" data-number="0" step="1" min="1" max="100" value="1"></div>
                                                                <span class="btn__quality-nav">
                                                                <span class="btn__quality-minus" data-prev-quality>-</span>
                                                                <span class="btn__quality-plus" data-next-quality>+</span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="btn"><button class="add-to-cart" value="{{$seed->id}}">Купить</button></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endguest
                                        </div>
                                    </div>
                                    <?php $count++ ?>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                    @else
                        <div class="row prodAttr" data-catalog <?= !empty($atrProd) ? $atrProd : 'data-catalog-grid'  ?>>
                            <?php $count = 0;?>
                            @foreach ($seeds as $seed)
                                    <div class="col-6 col-md-4 col-xl-2 fadeIn">
                                        <div class="box__product-item">
                                            <div class="wrapper-img">
                                                <div class="box__image"><a href="/product/{{$seed->id}}"><span
                                                            style="background-image: url( '{{Voyager::image($seed->images)}}' );"></span></a>
                                                </div>
                                            </div>
                                            <div class="wrapper-info">
                                                <div class="box__category"><a
                                                        href="/product/{{$seed->category->title}}">{{$seed->category->title}}</a>
                                                </div>
                                                <div class="box__title"><a href="/product/{{$seed->id}}">
                                                        <h3> {{$seed->title}} </h3></a>
                                                </div>
                                                <div class="box__description">
                                                    <div class="box__characteristics">
                                                        <ul>
                                                            <?php $specscount=0 ?>
                                                            @foreach($seed->subSpecification as $specs)
                                                                <li>{{$specs->specification}}: <span>{{$specs->title}}</span></li>
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
                                                                    <span class="box__price-sale">{{$seed->price}} ₽</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="box__quality">
                                                                <div class="box__quality-value"><input type="number" name="quantity[]" class="quantity{{$seed->id}}" data-number="0" step="1" min="1" max="100" value="1"></div>
                                                                <span class="btn__quality-nav">
                                                                <span class="btn__quality-minus" data-prev-quality>-</span>
                                                                <span class="btn__quality-plus" data-next-quality>+</span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="btn"><button class="add-to-cart" value="{{$seed->id}}">Купить</button></div>
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

@endsection


