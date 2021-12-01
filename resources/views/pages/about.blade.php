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

		<section class="box__about-head">
			<div class="container">
				<div class="row">
					<div class="col-12"><h1>{{ $info->title }}</h1></div>
				</div>
				<div class="row">
					<div class="col-12 col-xl-6 oeder-1 order-xl-2">
						<div class="box__slider-oneslides">
							<div class="swiper-container">
								<div class="swiper-wrapper">
									@foreach (json_decode($info->gallery) as $img)
										<div class="swiper-slide">
										<div class="box__image"><img src="{{ thumbImg($img, 695, 430, 1) }}" alt=""></div>
									</div>
									@endforeach
								</div>
								<div class="slider-oneslides-pagination"></div>
							</div>
						</div>
					</div>
					<div class="col-12 col-xl-6 oeder-2 order-xl-1">
						<div class="wrapper__about-head">
							{!! nl2br($info->text) !!}
						</div>
					</div>
				</div>
				<div class="row">
					@foreach ($ourGoals as $goal)
						<div class="col-12 col-md-6">
							<div class="box__about-item">
								<div class="box__icon"><span style="background-image: url( {{ thumbImg($goal->img, 35, 35, 0) }} );"></span></div>
								<h3>{{ $goal->title }}</h3>
								<div class="box__description"><p> {{ $goal->textlo }} </p></div>
							</div>
						</div>
					@endforeach
				</div>
				<div class="row">
					<div class="col-12">
						<div class="box__quote">
							<div class="box__image"><span style="background-image: url( {{ thumbImg($blockQuote->img, 710, 450, 1) }} );"></span></div>
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="box__quote-info">
										<div class="box__quote-icon"></div>
										<blockquote>
											{!! $blockQuote->text !!}
										</blockquote>
										<div class="box__quote-name">{{ $blockQuote->name }}<span>{{ $blockQuote->position }}</span></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h2>Мы в цифрах</h2>
					</div>
				</div>
				<div class="row">
					@foreach ($counters as $counter)
						<div class="col-12 col-md-6 col-xl-3">
							<div class="box__about-item">
								<h3 class="box__title-big">{{ $counter->title }}</h3>
								<div class="box__description"><p>{{ $counter->text }}</p></div>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</section>

		<section class="box__productsviewed">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<h2>Награды и сертификаты</h2>
						<div class="wrapper__nav-productsviewed">
							<div class="slider-productsviewed-prev"></div>
							<div class="slider-productsviewed-next"></div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="box__slider-productsviewed">
							<div class="swiper-container">
								<div class="swiper-wrapper">
									@foreach ($certificates as $certificate)
										<div class="swiper-slide">
											<div class="box__diploma">
												<a href="/storage/{{ $certificate->img }}" data-fancybox="diploma">
													<span style="background-image: url( {{ thumbImg($certificate->img, 260, 350, 1) }} )"></span>
												</a>
											</div>
										</div>
									@endforeach
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="box__about-head">
			<div class="container">
				<div class="row">
					<div class="col-12"><h2>Преимущества</h2></div>
				</div>
				<div class="row">
					@foreach ($advantages as $advantage)
						<div class="col-12 col-md-6 col-xl-4">
							<div class="box__about-item">
								<div class="box__icon"><span style="background-image: url( {{ thumbImg($advantage->img, 35, 35, 1) }} );"></span></div>
								<h3>{{ $advantage->title }}</h3>
								<div class="box__description"><p>{{ $advantage->text }}</p></div>
							</div>
						</div>
					@endforeach
				</div>
				<div class="row">
					<div class="col-12">
						<div class="box__brands">
							<h2>Широкий ассортимент марок</h2>
							<div class="box__description">
								<p>{{setting('site.aboutus_brandts_text')}}</p>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="box__brands-slider">
							<div class="swiper-container">
								<div class="swiper-wrapper">
									@foreach ($brands as $brand)
										<div class="swiper-slide">
											<div class="box__brands-item">
												<a href="#">
													<span style="background-image: url( {{ thumbImg($brand->img, 300, 300, 0) }} );"></span>
												</a>
											</div>
										</div>
									@endforeach
								</div>

								<div class="slider-brands-next"></div>
								<div class="slider-brands-prev"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</main>
@endsection
