@extends('layouts.app')
<?php
$showBuyButton = !(\Carbon\Carbon::parse($product->preorder->end_date))->isSameDay(\Carbon\Carbon::now());

    ?>
@section('content')
<script>
$(document).ready(function() {
  // Hide all tab content except the first one
  $('.tabcontent:not(:first)').hide();

  // Add click event to tab links
  $('.tablinks').click(function() {
    // Get the tab ID from data attribute
    var tab_id = $(this).data('tab');

    // Hide all tab content and remove active class from all tab links
    $('.tabcontent').hide();
    $('.tablinks').removeClass('active');

    // Show the selected tab content and add active class to the selected tab link
    $('#' + tab_id).show();
    $(this).addClass('active');
  });
});
  </script>
    <div class="box__breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul>
                        <li><a href="/">{{setting('site.main_title_buttom')}}</a></li>
                        <li><a href="/preorders/{{ $product->category->preorder_id }}">{{ $product->category->preorder->title }}</a></li>
                        <li><a href="/preorders/category/{{$product->category->id}}/products">{{$product->category->title}}</a></li>
                        <li> {{$product->title}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <section class="box__product">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>{{$product->title}}</h1>
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
                    <div class="col-12 col-md-5 col-lg-4 col-xl-3 col-xxl-4">
                        <div class="gallery-container">
                            <div class="gallery-main">
                                <div class="box__image">
                                    <img src="{{ thumbImg($product->image ?? $product->preorder->default_image, 300) }}" alt="{{$product->title}}">
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-12 col-md-7 col-lg-8 col-xl-9 offset-xxl-1 col-xxl-7">
                        <div class="tabs__content-item row">
                            <div class="p-15">
                                <div class="tablinks active" data-tab="tab1">{{setting('site.description_product')}}</div>
                            </div>
                            <div class="p-15">
                                <div class="tablinks" data-tab="tab2">Характеристики</div>
                            </div>
                          </div>
                          <div id="tab1" class="tabcontent">
                              <br>
                              <p>{{$product->description }}</p>
                          </div>
                          <div id="tab2" class="tabcontent">
                              <br>
                              <ul class="characteristics">
                                  @if(!empty($product->barcode)) <li>Штрихкод: <b>{{$product->barcode }}</b></li> @endif
                                  @if(!empty($product->container)) <li>Контейнер: <b>{{$product->container }}</b></li> @endif
                                  @if(!empty($product->country)) <li>Страна: <b>{{$product->country }}</b></li> @endif
                                  @if(!empty($product->packaging)) <li>Фасовка: <b>{{$product->packaging }}</b></li> @endif
                                  @if(!empty($product->package_type)) <li>Тип пакета: <b>{{$product->package_type }}</b></li> @endif
                                  @if(!empty($product->weight)) <li>Вес: <b>{{$product->weight }}</b></li> @endif
                                  @if(!empty($product->r_i)) <li>Р.И: <b>{{$product->r_i }}</b></li> @endif
                                  @if(!empty($product->season)) <li>Сезон: <b>{{$product->season }}</b></li> @endif
                                  @if(!empty($product->plant_height)) <li>Высота растения: <b>{{$product->plant_height }}</b></li> @endif
                                  @if(!empty($product->packaging_type)) <li>Вид упаковки: <b>{{$product->packaging_type }}</b></li> @endif
                                  @if(!empty($product->package_amount)) <li>Количество в упаковке: <b>{{$product->package_amount }}</b></li> @endif
                                  @if(!empty($product->culture_type)) <li>Вид культуры: <b>{{$product->culture_type }}</b></li> @endif
                                  @if(!empty($product->frost_resistance)) <li>Морозостойкость: <b>{{$product->frost_resistance }}</b></li> @endif
                                  @if(!empty($product->additional_1)) <li>Доп. информация: <b>{{$product->additional_1 }}</b></li> @endif
                                  @if(!empty($product->additional_2)) <li>Доп. информация: <b>{{$product->additional_2 }}</b></li> @endif
                                  @if(!empty($product->additional_3 )) <li>Доп. информация: <b>{{$product->additional_3 }}</b></li> @endif
                                  @if(!empty($product->additional_4 )) <li>Доп. информация: <b>{{$product->additional_4 }}</b></li> @endif
                              </ul>

                          </div>


                        {{-- <div class="box__tabs">
                            <ul>
                                <li class="box-tab active" data-tab="description">{{setting('site.description_product')}}</li>
                            </ul>
                        </div>
                        <div data-tab="description" class="box__tab-content active" style="min-height: 120px">
                            <div class="box__product-description">
                                <p>{{$product->description }}</p>
                            </div>
                        </div> --}}

                        @if (\Illuminate\Support\Facades\Auth::check() && auth()->user()->active != 'off' && $showBuyButton)
                            <div class="wrapper__product-bottom">
                                <div class="box__product-price">
                                    @if(!empty($product->new_price))
                                        <span class="box__price-normal">{{$product->price}} ₽</span>
                                        <span class="box__price-sale">{{$product->new_price}} ₽</span>
                                    @else
                                        <span class="box__price-normal">{{$product->price}} ₽</span>
                                    @endif
                                </div>
                                <div class="box__product-status">в наличии</div>
                                <div class="box__product-quality">
                                    <div class="box__quality">
                                        <div class="box__quality-value"><input type="number" name="quantity"
                                                                               class="quantity{{ $product->id }}"
                                                                               data-number="{{ $product->multiplicity }}"
                                                                               step="{{ $product->multiplicity }}"
                                                                               min="{{ $product->multiplicity }}"
                                                                               max="{{ $product->total }}"
                                                                               value="{{ $product->multiplicity }}"
                                                                               data-id="{{ $product->id }}"></div>
                                        <span class="btn__quality-nav">
                                                <span class="btn__quality-minus update-cart" data-id="{{$product->id}}"
                                                      data-prev-quality>-</span>
                                                <span class="btn__quality-plus update-cart" data-id="{{$product->id}}"
                                                      data-next-quality>+</span>
                                            </span>
                                    </div>
                                </div>
                                <div class="btn"><a
                                        class="add-to-cart-preorder {{ $cartKeys->contains($product->id) ? 'ifcart' : '' }}"
                                        value="{{ $product->id }}">{{ $cartKeys->contains($product->id) ? 'Докупить' : 'Купить' }}</a>
                                    <div class="ifcart">@if($cartKeys->contains($product->id))Товар есть в
                                        корзине@endif</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .p-15{
            padding: 15px;
        }
        .characteristics{
            margin-left: 0;
        }
        .characteristics li{
            list-style: none;
            margin: 10px 0 0 0;
        }
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
