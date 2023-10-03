<div class="baskets-info-floating container">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-3">
                    <div class="box__image"><span
                            style="height:120px!important; width:80px!important;
                    background-image: url( {{ thumbImg($product->image ??(!is_null($preorder->default_image) ? $preorder->default_image : ''), 80, 120) }} );"></span>
                    </div>
                </div>
                <div class="col-9">
                    <a href="/preorders/product/{{ $product->id }}">
                        <b>{{$product->title}}</b>
                    </a>
                    <ul class="characteristics">
                        @if(!empty($product->barcode))
                            <li>Штрихкод: <b>{{$product->barcode }}</b></li>
                        @endif
                        @if(!empty($product->container))
                            <li>Контейнер: <b>{{$product->container }}</b></li>
                        @endif
                        @if(!empty($product->country))
                            <li>Страна: <b>{{$product->country }}</b></li>
                        @endif
                        @if(!empty($product->packaging))
                            <li>Фасовка: <b>{{$product->packaging }}</b></li>
                        @endif
                        @if(!empty($product->package_type))
                            <li>Тип пакета: <b>{{$product->package_type }}</b></li>
                        @endif
                        @if(!empty($product->weight))
                            <li>Вес: <b>{{$product->weight }}</b></li>
                        @endif
                        @if(!empty($product->r_i))
                            <li>Р.И: <b>{{$product->r_i }}</b></li>
                        @endif
                        @if(!empty($product->season))
                            <li>Сезон: <b>{{$product->season }}</b></li>
                        @endif
                        @if(!empty($product->plant_height))
                            <li>Высота растения: <b>{{$product->plant_height }}</b></li>
                        @endif
                        @if(!empty($product->packaging_type))
                            <li>Вид упаковки: <b>{{$product->packaging_type }}</b></li>
                        @endif
                        @if(!empty($product->package_amount))
                            <li>Количество в упаковке: <b>{{$product->package_amount }}</b></li>
                        @endif
                        @if(!empty($product->culture_type))
                            <li>Вид культуры: <b>{{$product->culture_type }}</b></li>
                        @endif
                        @if(!empty($product->frost_resistance))
                            <li>Морозостойкость: <b>{{$product->frost_resistance }}</b></li>
                        @endif
                        @if(!empty($product->additional_1))
                            <li>Доп. информация: <b>{{$product->additional_1 }}</b></li>
                        @endif
                        @if(!empty($product->additional_2))
                            <li>Доп. информация: <b>{{$product->additional_2 }}</b></li>
                        @endif
                        @if(!empty($product->additional_3 ))
                            <li>Доп. информация: <b>{{$product->additional_3 }}</b></li>
                        @endif
                        @if(!empty($product->additional_4 ))
                            <li>Доп. информация: <b>{{$product->additional_4 }}</b></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="product-description">
                {{$product->description}}
            </div>
        </div>
    </div>
</div>
