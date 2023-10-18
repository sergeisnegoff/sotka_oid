<div class="wrapper__baskets-item" id="merch-product-{{$product->id}}">
    <div class="row">
        <div class="col-12 col-md-3">
            <div class="wrapper__baskets-info">
                <div class="box__image"><span
                        style="background-image: url( {{ thumbImg($product->image ??(!is_null($preorder->default_image) ? $preorder->default_image : ''), 50, 70) }} );"></span>
                </div>
                <a href="/preorders/product/{{ $product->id }}">
                    <h3>{{ $product->title }}</h3></a>
                @include('merch.components.list.hover')
            </div>
        </div>
        <div class="col-12 col-md-1">
            <div class="wrapper__baskets-quality">
                                                            <span
                                                                class="wrapper__baskets-titlequality">Количество:</span>
                <div class="box__quality">
                    <div class="box__quality-value text-center">
                        {{$product->multiplicity}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-1">
            <div class="wrapper__baskets-price"><span>Цена:</span>
                {{ $product->merch_price }} ₽
            </div>
        </div>
        <div class="col-12 col-md-1">
            {{$product->soft_limit}}
            @if ($product->hard_limit)
                <br/>
                {{$product->hard_limit}}
            @endif
        </div>
        <div class="col-12 col-xl-2 wrapper-total-quantity">
            Клиенты: {{$product->getTotalQtyByType()}} шт <br/>
            Сотка: {{$product->getTotalQtyByType(true)}} шт <br/>
            Всего: {{$product->getTotalQty()}} шт
        </div>
        <div class="col-12 col-md-2">
            <div class="box__quality">
                <div class="box__quality-value">
                    <input type="number"
                           data-number="0"
                           step="1"
                           name="merch-quantity"
                           data-product-id="{{$product->id}}"
                           value="0">
                </div>
                <span class="btn__quality-nav">
                    <span class="btn__quality-minus merch-change-qty"
                          data-op="decrement"
                          data-id="{{$product->id}}">-</span>
                    <span class="btn__quality-plus merch-change-qty"
                          data-op="increment"
                          data-id="{{$product->id}}">+</span>
                </span>

            </div>
        </div>
        <div class="col-12 col-md-2">
            <div class="btn {{$product->getTotalQtyByType(true) ? 'ifcart' : ''}}">
                <button class="qty-append" data-id="{{$product->id}}">Применить
                </button>
            </div>
        </div>
    </div>
</div>
