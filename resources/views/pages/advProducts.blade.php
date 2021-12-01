@extends('layouts.app')

@section('content')
    <div class="box__breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul>
                        <li><a href="/">Главная</a></li>
                        <li>{{ $info->title }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <section class="box__promotionalproducts">
        <div class="container">
            <div class="row">
                <div class="col-12"><h1>{{ $info->title }}</h1></div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="box__promotionalproducts-tab">
                        <div class="wrapper__promotionalproducts-active">Каталоги</div>
                        <ul>
                            <li class="active" data-tab="total"><button>Все</button></li>
                            @foreach ($items as $key => $category)
                                <li class="" data-tab="{{ \Illuminate\Support\Str::slug($category->title) }}"><button>{{ $category->title }}</button></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="box__promotionalproducts-content active" data-tab="total">
                <div class="row">
                    @foreach ($items as $category)
                        @foreach ($category->items as $item)
                            @php($files = json_decode($item->file))
                            @if (empty($files) || is_null($files))
                                @continue
                            @endif
                            @php($file = json_decode($item->file))
                            <div class="col-12 col-xl-2">
                                <div class="box__product-bigitem">
                                    <div class="wrapper-img">
                                        <div class="box__image"><a><span style="background-image: url({{ thumbImg($item->img, 260, 370) }});"></span></a></div>
                                    </div>
                                    <div class="wrapper-info">
                                        <div class="box__title"><a ><h3>{{ $item->title }}</h3></a></div>
                                    </div>
                                    <div class="wrapper-button">
                                        <div class="btn"><a href="/storage/{{ $file[0]->download_link }}" download="">Скачать</a></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
            @foreach ($items as $category)
                <div class="box__promotionalproducts-content" data-tab="{{ \Illuminate\Support\Str::slug($category->title) }}">
                <div class="row">
                    @foreach ($category->items as $item)
                        @php($files = json_decode($item->file))
                        @if (empty($files) || is_null($files))
                            @continue
                        @endif
                        @php($file = json_decode($item->file))
                        <div class="col-12 col-xl-2">
                            <div class="box__product-bigitem">
                                <div class="wrapper-img">
                                    <div class="box__image"><a><span style="background-image: url({{ thumbImg($item->img, 260, 370) }});"></span></a></div>
                                </div>
                                <div class="wrapper-info">
                                    <div class="box__title"><a><h3>{{ $item->title }}</h3></a></div>
                                </div>
                                <div class="wrapper-button">
                                    <div class="btn"><a href="/storage/{{ $file[0]->download_link }}" download="">Скачать</a></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </section>
@endsection
