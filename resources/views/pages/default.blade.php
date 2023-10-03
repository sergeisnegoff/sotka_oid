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
                    <div class="col-12 offset-xl-1 col-xl-10 offset-xxl-2 col-xxl-8">
                        <div class="box__life-item">
                            <div class="box__image">
                                @if(!is_null($info->gallery))
                                    <span style="background-image: url( {{ thumbImg(json_decode($info->gallery)[0], 935, 420, 1)  }} );">
                                </span>
                                @endif
                            </div>
                            <div class="wrapper-info">
                                {!! nl2br($info->text) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
