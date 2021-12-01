@extends('layouts.app')

@section('content')
    <main>
        <div class="box__breadcrumbs">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <ul>
                            <li><a href="/">Главная</a></li>
                            <li>Ошибка 500</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <section class="box__error-page">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1><span>5<b>0</b>0</span>Ошибка сервера <br/> Попробуйте позже</h1>
                        <div class="btn"><a href="/">Вернуться на главную</a></div>
                    </div>
                </div>
            </div>
        </section>

    </main>
@endsection
