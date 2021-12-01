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
					@foreach ($ourLife as $item)
						<div class="col-12 offset-xl-1 col-xl-10 offset-xxl-2 col-xxl-8">
							<div class="box__life-item">
								<div class="box__data">
									<span>{{ date('d', strtotime($item->date)) }}</span> {{ rusDate(date('m', strtotime($item->date))) }}
								</div>
								<div class="box__image">
									<a href="/our-life/{{ $item->id }}">
										<span style="background-image: url( {{ thumbImg($item->img, 935, 420, 1)  }} );">
										</span>
									</a>
								</div>
								<div class="wrapper-info">
									<h3>{{ $item->title }}</h3>
									<div class="box__link"><a href="/our-life/{{ $item->id }}">Подробнее</a></div>
								</div>
							</div>
						</div>
					@endforeach
				</div>
				{{--<div class="row">
					<div class="col-12">
						<div class="btn btn-white text-center" style="margin: 25px 0 0;"><a href="#">Показать еще</a>
						</div>
					</div>
				</div>--}}
			</div>
		</section>
	
	</main>
@endsection