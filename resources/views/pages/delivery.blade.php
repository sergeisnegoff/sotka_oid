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

		<section class="box__delivery-page">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<h1>{{ $info->title }}</h1>
					</div>
				</div>
				<div class="row">
					<div class="col-12 offset-xl-1 col-xl-10 offset-xxl-2 col-xxl-8">
						<div class="box__accodion">
							@foreach ($faq as $item)
								<div class="box__accodion-item">
									<h3>{{ $item->title }}<span class="box__accodion-arrow"></span></h3>
									<div class="box__content">
										<p>{!! nl2br($item->text) !!}</p>
									</div>
								</div>
							@endforeach
						</div>
					</div>
				</div>
			</div>
		</section>

	</main>
@endsection

@section('script')
    <script>
        $(function () {
            $('.box__accodion-item > h3').click(function () {
                if (!$(this).parent().find('.box__content').is(':visible')){
                    $('.box__accodion').find('.box__content').slideUp();
                    $('.box__accodion').find('.box__accodion-arrow').removeClass('reverse');

                } else {
                    $('.box__accodion').find('.box__content').slideUp();
                    $('.box__accodion').find('.box__accodion-arrow').removeClass('reverse');
                }
            })
        })
    </script>

    <style>
        .box__accodion-arrow.reverse {
            transform: rotate(180deg);
        }
    </style>
@endsection
