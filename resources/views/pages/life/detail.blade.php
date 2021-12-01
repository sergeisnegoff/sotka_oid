@extends('layouts.app')

@section('content')
	<main>

		<div class="box__breadcrumbs">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<ul>
							<li><a href="/">Главная</a></li>
							<li><a href="{{ $info->slug }}">{{ $info->title }}</a></li>
							<li>{{ $detailInfo->title }}</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<section class="box__life">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<h2>{{ $detailInfo->title }}</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-12 offset-xl-1 col-xl-10 offset-xxl-2 col-xxl-8">
						<div class="box__life-item">
							<div class="box__data">
								<span>{{ date('d', strtotime($detailInfo->date)) }}</span> {{ rusDate(date('m', strtotime($detailInfo->date))) }}
							</div>
							<div class="box__image">
								<a href="/our-life/{{ $detailInfo->id }}">
									<span style="background-image: url( {{ thumbImg($detailInfo->img, 935, 420, 1)  }} );">
									</span>
								</a>
							</div>
							<div class="wrapper-info">
								<h3>{{ $detailInfo->title }}</h3>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						{!! nl2br($detailInfo->text) !!}
					</div>
				</div>
			</div>
		</section>
	</main>
@endsection
