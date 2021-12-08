@extends('layouts.app')
@section('content')
<div class="box__breadcrumbs">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <ul>
                    <li><a href="/">Главная</a></li>
                    <li>Корзина</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<section class="box__basket-productdviewed">
    <div class="container">
        <div class="row">
            <div class="col-12"><h2>Корзина</h2></div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="box__basketpage">
                    <div class="row">
                        <div class="col-12 order-2 order-xl-1 col-xl-8">
                            <?php $totalAll = 0 ?>
                            <?php $total = 0 ?>
                                @foreach(session('cart') as $id => $details)
                                    @if(!empty($details['new_price']))
                                        <?php $total += $details['new_price'] * $details['quantity'] ?>
                                    @else
                                        <?php $total += $details['price'] * $details['quantity'] ?>
                                    @endif
                            <div class="wrapper__bascket-items">
                                <div class="box__basketpage-item">
                                    <div class="row">
                                        <div class="col-12 col-xl-5">
                                            <div class="wrapper__bascket-info">
                                                <div class="box__basketpage-image"><a href="#"><span style="background-image: url({{ Voyager::image( $details['images'] ) }});"></span></a></div>
                                                <div class="basketpage__item-name"><a href="#">{{$details['title']}}</a></div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-xl-2">
                                            <div class="basketpage__item-price">
                                                <span>Стоимсоть: </span>
                                                @if(!empty($details['new_price']))
                                                    <del> {{$details['price']}} ₽ </del> {{$details['new_price']}} ₽
                                                @else
                                                    {{$details['price']}} ₽
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-4 col-xl-2">
                                            <div class="basketpage__item-quality">
                                                <div class="box__quality">
                                                    <div class="box__quality-value">
                                                        <input type="number" data-number="0" step="{{ $seed->multiplicity }}" min="1" max="{{ $details['total'] }}" value="{{$details['quantity']}}">
                                                    </div>
                                                    <span class="btn__quality-nav">
                                                        <span class="btn__quality-minus" data-prev-quality>-</span>
                                                        <span class="btn__quality-plus" data-next-quality>+</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-xl-3">
                                            <?php $total += session()->get( 'cart.price') * session()->get( 'cart.quantity');
                                                $totalAll += $total;
                                            ?>
                                            <div class="basketpage__item-allprice"><span>Общая стоимсоть: </span>
                                            {{$total}} ₽
                                         </div>
                                        </div>
                                    </div>
                                    <div class="btn btn__item-delete remove-from-cart"><a href="#"></a></div>
                                </div>
                            </div>
                                @endforeach
                                <?php
                                $percent = 0;
                                $query = \Illuminate\Support\Facades\DB::table('salesystems')->select('procent')->where('sum_from','<=',$totalAll)->where('sum_to','>=',$totalAll)->get();
                                foreach ($query as $q) {
                                    $percent = $q;
                                }
                                ?>
                            <div class="wrapper__bascket-bottom">
                                <div class="row">
                                    <div class="col-6"></div>
                                    <div class="col-6">
                                        <div class="box__bascket-total">
                                            <h4><span>Итого: </span>
                                            @if($percent > 0)
                                               {{ ($totalAll*$percent)/100 }} ₽
                                            @else
                                                {{ $totalAll }} ₽
                                            @endif
                                        </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 order-1 order-xl-2 col-xl-4">
                            <div class="wrapper__basket-total">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="box__basket-title">
                                            <h4>Итого:</h4>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        @if($percent > 0)
                                            <div class="box__basket-price">{{ ($totalAll*$percent)/100 }} ₽</div>
                                        @else
                                             <div class="box__basket-price">{{ $totalAll }} ₽ </div>
                                        @endif

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12"><br/></div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="box-bottom">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="btn"><button>Отправить</button></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<br/>

@include('viewedProducts')
@endsection
