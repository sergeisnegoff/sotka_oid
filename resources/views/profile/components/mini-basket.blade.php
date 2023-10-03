@foreach($miniCartData as $details)
    <div class="box__basket-item">
        <div class="row">
            <div class="col-3">
                <div class="box__image"><a href="#"><img
                            src="{{ Voyager::image( $details['images'] ) }}" alt=""></a></div>
            </div>
            <div class="col-9">
                <a href="#" class="item_remove remove-from-cart" data-id="{{ $details['id'] }}">x</a>
                <div class="row">
                    <div class="col-12"><a href="/product/{{$details['id']}}"><h3>{{$details['title']}}</h3>
                        </a>
                    </div>
                    <div class="col-5">
                        <div class="box__quality">
                            <div class="box__quality-value"><input type="number" data-number="0"
                                                                   step="{{ $details['multiplicity'] }}"
                                                                   min="1"
                                                                   max="{{ $details['total'] }}"
                                                                   name="quantity[]"
                                                                   class="quantityUpdate{{ $details['id'] }}"
                                                                   value="{{$details['quantity']}}">
                            </div>
                            @if ($details['multiplicity'] <= $details['total_all'])
                                <span class="btn__quality-nav">
                                        <span class="btn__quality-minus update-cart" data-id="{{ $details['id'] }}"
                                              data-prev-quality>-</span>
                                        <span class="btn__quality-plus update-cart" data-id="{{ $details['id'] }}"
                                              data-next-quality>+</span>
                                    </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-7">
                        <div
                            class="box__price"> {{ $details['price'] * $details['quantity'] }}
                            <span>₽</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
<div class="wrapper-popup-bottom">
    <div class="row">
        <div class="col-6">
            <div class="box__price-title">Итого:</div>
        </div>
        <div class="col-6 text-right">
            <div class="box__price">{{ $miniCartTotal }} <span>₽</span></div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @if ($miniCartTotal > 0)
            <div class="btn"><a href="{{route('profile.orders.cart')}}">В корзину</a></div>
            @endif
        </div>
    </div>
</div>
