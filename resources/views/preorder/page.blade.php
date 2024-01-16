@extends('layouts.app')

@section('content')

    @push('styles')
        <style>
            .mySlides {
                display: none
            }

            img {
                vertical-align: middle;
            }

            /* Slideshow container */
            .slideshow-container {
                max-width: 1000px;
                position: relative;
                margin: auto auto 20px;
            }

            /* Next & previous buttons */
            .prev, .next {
                cursor: pointer;
                position: absolute;
                top: 50%;
                width: auto;
                padding: 16px;
                margin-top: -22px;
                color: white;
                font-weight: bold;
                font-size: 18px;
                transition: 0.6s ease;
                border-radius: 0 3px 3px 0;
                user-select: none;
            }

            /* Position the "next button" to the right */
            .next {
                right: 0;
                border-radius: 3px 0 0 3px;
            }

            /* On hover, add a black background color with a little bit see-through */
            .prev:hover, .next:hover {
                background-color: rgba(0, 0, 0, 0.8);
            }

            /* Number text (1/3 etc) */
            .numbertext {
                color: #f2f2f2;
                font-size: 12px;
                padding: 8px 12px;
                position: absolute;
                top: 0;
            }

            /* The dots/bullets/indicators */
            .dot {
                cursor: pointer;
                height: 15px;
                width: 15px;
                margin: 0 2px;
                background-color: #bbb;
                border-radius: 50%;
                display: inline-block;
                transition: background-color 0.6s ease;
            }

            .active-button, .dot:hover {
                background-color: #717171;
            }

            /* Fading animation */
            .fade {
                animation-name: fade;
                animation-duration: 1.5s;
            }

            @keyframes fade {
                from {
                    opacity: .4
                }
                to {
                    opacity: 1
                }
            }

            /* On smaller screens, decrease text size */
            @media only screen and (max-width: 300px) {
                .prev, .next, .text {
                    font-size: 11px
                }
            }
        </style>
    @endpush
    <main>
        <div class="box__breadcrumbs">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <ul>
                            <li><a href="/">Главная</a></li>
                            <li><a href="/preorders">Предзаказы</a></li>
                            <li><a href="/preorders/{{$info->id}}">{{ $info->title }}</a></li>
                            <li>Описание</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="text-center">
                <h2>{{ $info->title }}</h2>
            </div>
            <div class="row">
                <div style="margin: auto">
                    <img width="1200" height="700" src="/storage/{{ $info?->background_image  }}">
                </div>

                <div class="col-12">
                    <div class="wrapper-info" style="width: 70%; margin: auto">
                        <div class="m-auto" style="padding: 50px">
                            {!! nl2br($info->short_description) !!}
                        </div>
                        @if($info->slide_images && count(json_decode($info->slide_images) ?? []))
                            <div class="slideshow-container">
                                @foreach(json_decode($info->slide_images) as $image)
                                    <div class="mySlides fade" style="text-align:center">
                                        <div class="numbertext">{{ $loop->iteration }} / {{ $loop->count }}</div>
                                        <img src="/storage/{{ $image }}" style="width:auto; max-height:600px;">
                                    </div>
                                @endforeach


                                <a class="prev" onclick="plusSlides(-1)">❮</a>
                                <a class="next" onclick="plusSlides(1)">❯</a>

                            </div>
                            <div style="text-align:center">
                                @if($info->slide_images && count(json_decode($info->slide_images)))
                                    @foreach(json_decode($info->slide_images) as $image)
                                        <span class="dot" onclick="currentSlide(@js($loop->iteration))"></span>
                                    @endforeach
                                @endif
                            </div>
                        @endif
                        <div class="m-auto" style="padding: 50px">
                            {!! nl2br($info->description) !!}
                        </div>
                        <br>



                        <a
                            href="/preorders/{{ $info->id }}"
                            style="border:2px solid #6dac52;border-radius:48px;padding:10px 20px;color:black;display:block;width:max-content;margin:0 auto 10px;text-decoration:none">
                            Оформить предзаказ
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="box__life">
            <div class="container">
                <div class="row">
                    @foreach ($preorders as $item)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="box__life-item"
                                 style="border: 1px solid silver;border-radius: 5px;overflow:hidden;">
                                <div class="box__image">
                                    <a href="/preorders/{{ $item->id }}">
										<span style="background-image: url( /storage/{{ $item->image }} );">
										</span>
                                    </a>
                                </div>
                                <div class="box__description" style="background-color:white;padding:10px 0;">
                                    <p style="font-size: 1.5rem;text-align:center;font-weight:bolder;margin-bottom:10px;">{{ $item->title }}</p>
                                    <p style="text-align:center;font-size: 0.85rem;">Окончание приёма
                                        заявок {{ \Carbon\Carbon::parse($item->end_date)->format('d.m.Y') }}</p>
                                    <a href="/preorders/{{ $item->id }}"
                                       style="border:2px solid #6dac52;border-radius:48px;padding:10px 15px;color:black;display:block;width:max-content;margin:0 auto 10px;text-decoration:none">Оформить
                                        предзаказ</a>
                                    <div class="box__link"><a
                                            style="text-align:center;display:block;width:max-content;margin:0 auto;"
                                            href="/preorders/info/{{ $item->id }}">Подробнее</a></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

    </main>

    @push('scripts')
        <script>
            let slideIndex = 1;
            showSlides(slideIndex);

            function plusSlides(n) {
                showSlides(slideIndex += n);
            }

            function currentSlide(n) {
                showSlides(slideIndex = n);
            }

            function showSlides(n) {
                let i;
                let slides = document.getElementsByClassName("mySlides");
                let dots = document.getElementsByClassName("dot");
                if (n > slides.length) {
                    slideIndex = 1
                }
                if (n < 1) {
                    slideIndex = slides.length
                }
                for (i = 0; i < slides.length; i++) {
                    slides[i].style.display = "none";
                }
                for (i = 0; i < dots.length; i++) {
                    dots[i].className = dots[i].className.replace(" active-button", "");
                }
                slides[slideIndex - 1].style.display = "block";
                dots[slideIndex - 1].className += " active-button";
            }
        </script>
    @endpush
@endsection
