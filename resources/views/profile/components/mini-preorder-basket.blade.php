<?php $miniCartTotal = 0 ?>
@foreach($miniCartData as $details)
    <div class="box__basket-item">
        <div class="row">
            <div class="col-3">
                <div class="box__image"><a href="#"><img
                            src="/storage/{{ $details['image'] }}" alt=""></a></div>
            </div>
            <div class="col-9">
                <a href="#" class="item_remove remove-from-cart" data-id="{{ $details['id'] }}">x</a>
                <div class="row">
                    <div class="col-12"><a href="/preorders/product/{{$details['id']}}"><h3>{{$details['name']}}</h3>
                        </a>
                    </div>
                    <div class="col-5">
                        <div class="box__quality">
                            <div class="box__quality-value"><input type="number" data-number="0"
                                                                   step="{{ $details['multiplicity'] }}"
                                                                   min="1"
                                                                   name="quantity[]"
                                                                   class="quantityUpdate{{ $details['id'] }}"
                                                                   value="{{$details['quantity']}}">
                            </div>
                                <span class="btn__quality-nav">
                                    <span class="btn__quality-minus update-cart" data-id="{{ $details['id'] }}"
                                          data-prev-quality>-</span>
                                    <span class="btn__quality-plus update-cart" data-id="{{ $details['id'] }}"
                                          data-next-quality>+</span>
                                </span>
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
    <?php $miniCartTotal += $details['price'] * $details['quantity'] ?>
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
            <div class="btn"><a href="/preorders/cart">В корзину</a></div>
        </div>
    </div>
</div>
