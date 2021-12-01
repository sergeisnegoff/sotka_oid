@foreach ($cart as $id => $details)
    @php($product = \App\Product::multiplicity()->find($id))
    <div class="box__basket-item">
        <div class="row">
            <div class="col-3">
                <div class="box__image"><a href="#">
                    @if (!empty($details['images']))
                        <img
                            src="{{ Voyager::image( $details['images'] ) }}" alt=""></a></div>
                    @endif
            </div>
            <div class="col-9">
                <a href="#" class="item_remove remove-from-cart" data-id="{{ $id }}">x</a>
                <div class="row">
                    <div class="col-12"><a href="product/{{$id}}"><h3>{{$details['title']}}</h3></a></div>
                    <div class="col-5">
                        <div class="box__quality">
                            <div class="box__quality-value"><input type="number" data-number="0"
                                                                   step="{{ $product->multiplicity }}"
                                                                   max="{{ $details['total'] }}"
                                                                   min="1"
                                                                   name="quantity[]"
                                                                   class="quantityUpdate{{ $id }}"
                                                                   value="{{ $product->multiplicity }}"
                                                                   readonly="">
                            </div>
{{--                            @if ($product->multiplicity <= $product->total)--}}
                                <span class="btn__quality-nav">
                                                <span class="btn__quality-minus update-cart" data-id="{{ $id }}"
                                                      data-prev-quality>-</span>
                                                <span class="btn__quality-plus update-cart" data-id="{{ $id }}"
                                                      data-next-quality>+</span>
                                            </span>
{{--                            @endif--}}
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="box__price"> {{ $details['price'] * $details['quantity'] }} <span>₽</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
