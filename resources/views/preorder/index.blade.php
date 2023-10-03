@extends('layouts.app')

@section('content')
    <main>
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

        <section class="box__life">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2>{{ $info->title }}</h2>
                    </div>
                </div>
                <div class="row">
                    @foreach ($preorders as $item)
                        <div class="col-4">
                            <div class="box__life-item" style="border: 1px solid silver;border-radius: 5px;overflow:hidden;">
                                <div class="box__image">
                                    <a href="/preorders/{{ $item->id }}">
										<span style="background-image: url( /storage/{{ $item->image }} );">
										</span>
                                    </a>
                                </div>
                                <div class="box__description" style="background-color:white;padding:10px 0;">
                                    <p style="font-size: 1.5rem;text-align:center;font-weight:bolder;margin-bottom:10px;">{{ $item->title }}</p>
                                    <p style="text-align:center;font-size: 0.85rem;">Окончание приёма заявок {{ \Carbon\Carbon::parse($item->end_date)->format('d.m.Y') }}</p>
                                    <a href="/preorders/{{ $item->id }}" style="border:2px solid #6dac52;border-radius:48px;padding:10px 15px;color:black;display:block;width:max-content;margin:0 auto 10px;text-decoration:none">Оформить предзаказ</a>
                                    <div class="box__link"><a style="text-align:center;display:block;width:max-content;margin:0 auto;" href="/preorders/info/{{ $item->id }}">Подробнее</a></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>
@endsection
