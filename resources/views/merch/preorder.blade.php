@extends('layouts.app')

@section('content')
    <section class="box__personalarea">
        <div class="container">
            <div class="row text-center">
                <h1 style="width:100%; margin-top:20px;">{{$preorder->title}}</h1>
            </div>
            @include('merch.components.preorder-main-info')
            @include('merch.components.preorder-tabs')
            <div class="row">
                <div class="col-12">
                    <div class="col-12"  style="display: flex; flex-flow: row; margin-top:25px;">
                    @include('merch.components.subcategory')
                    </div>
                    <div class="wrapper__currentorder-info">

                        <div class="box__basketpage">
                            <div class="row">
                                <div class="col-12">
                                    <div class="wrapper__baskets">
                                        <div class="wrapper__baskets-title">
                                            <div class="row">
                                                <div class="col-12 col-md-3"><h4>Наименование</h4></div>
                                                <div class="col-12 col-md-1"><h4>Упаковка</h4></div>
                                                <div class="col-12 col-md-1"><h4>Цена</h4></div>
                                                <div class="col-12 col-md-1"><h4>Лимит</h4></div>
                                                <div class="col-12 col-md-2"><h4>Сводка</h4></div>
                                            </div>
                                        </div>
                                        <div class="products-container">
                                        @foreach ($products as $product)
                                            @include('merch.components.list.element')
                                        @endforeach
                                        </div>
                                        @include('merch.components.list.js')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="height:150px; width:100%;"></div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@8.2.2/dist/css/autoComplete.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@8.2.2/dist/js/autoComplete.min.js"></script>
    <script src="{{ asset('js/libs/izimodal/js/iziModal.js') }}"></script>

@endsection

