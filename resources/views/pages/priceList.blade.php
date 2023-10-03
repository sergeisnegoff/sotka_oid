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

		<section class="box__price-page">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<h1>{{ $info->title }}</h1>
					</div>
				</div>
				<div class="row">
					@foreach ($priceLists as $priceList)
                        @php($files = json_decode($priceList->file))
                        @continue(empty($files))
						@php($fileURL = $files[0])
						<div class="col-12 col-xl-2">
							<div class="box__price-item">
								<div class="wrapper-img">
									<div class="box__image"><a href="{{ \Illuminate\Support\Facades\Storage::url($fileURL->download_link) }}" download="{{ $fileURL->original_name }}"><span style="background-image: url( {{ thumbImg($priceList->img, 260, 370) }} );"></span></a></div>
								</div>
								<div class="wrapper-info">
									<div class="box__title"><a href="{{ \Illuminate\Support\Facades\Storage::url($fileURL->download_link) }}" download="{{ $fileURL->original_name }}"><h3>{{ $priceList->title }}</h3></a></div>
								</div>
								<div class="wrapper-button">
									<div class="btn"><a href="{{ \Illuminate\Support\Facades\Storage::url($fileURL->download_link) }}" download="{{ $fileURL->original_name }}">Скачать</a></div>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</section>

	</main>
@endsection
