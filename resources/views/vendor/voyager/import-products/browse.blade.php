@extends('voyager::master')

@section('page_title', __('voyager::generic.viewing').' '.$dataType->getTranslatedAttribute('display_name_plural'))

@php
    $cronSettingsProducts = \App\Models\cronSettings::where('table', 'import_products')->first();
    $cronSettingsContr = \App\Models\cronSettings::where('table', 'import_contr')->first();

@endphp

@section('content')
    <div class="page-content browse container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="h3">Импорт товаров</p>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-bordered"></table>
                            </div>
                            <div class="col-md-12">
                                <form class="row save-cron">
                                    <input type="hidden" name="table" value="import_products">
                                    <div class="col-md-2"><input type="text" name="minute" class="form-control" placeholder="минута" value="{{ $cronSettingsProducts ? $cronSettingsProducts->minute  : '*' }}"></div>
                                    <div class="col-md-2"><input type="text" name="hour" class="form-control" placeholder="час" value="{{ $cronSettingsProducts ? $cronSettingsProducts->hour  : '*' }}"></div>
                                    <div class="col-md-2"><input type="text" name="day" class="form-control" placeholder="день" value="{{ $cronSettingsProducts ? $cronSettingsProducts->day  : '*' }}"></div>
                                    <div class="col-md-2"><input type="text" name="month" class="form-control" placeholder="месяц" value="{{ $cronSettingsProducts ? $cronSettingsProducts->month  : '*' }}"></div>
                                    <div class="col-md-2"><input type="text" name="week_day" class="form-control" placeholder="день недели" value="{{ $cronSettingsProducts ? $cronSettingsProducts->week_day  : '*' }}"></div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary" style="margin-top: 0;margin-bottom: 0;">Сохранить</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="h3">Импорт товаров</p>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-bordered"></table>
                            </div>
                            <div class="col-md-12">
                                <form class="row save-cron">
                                    <input type="hidden" name="table" value="import_contr">
                                    <div class="col-md-2"><input type="text" name="minute" class="form-control" placeholder="минута" value="{{ $cronSettingsContr ? $cronSettingsContr->minute  : '*' }}"></div>
                                    <div class="col-md-2"><input type="text" name="hour" class="form-control" placeholder="час" value="{{ $cronSettingsContr ? $cronSettingsContr->hour  : '*' }}"></div>
                                    <div class="col-md-2"><input type="text" name="day" class="form-control" placeholder="день" value="{{ $cronSettingsContr ? $cronSettingsContr->day  : '*' }}"></div>
                                    <div class="col-md-2"><input type="text" name="month" class="form-control" placeholder="месяц" value="{{ $cronSettingsContr ? $cronSettingsContr->month  : '*' }}"></div>
                                    <div class="col-md-2"><input type="text" name="week_day" class="form-control" placeholder="год" value="{{ $cronSettingsContr ? $cronSettingsContr->week_day  : '*' }}"></div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary" style="margin-top: 0;margin-bottom: 0;">Сохранить</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop

@section('css')
@stop

@section('javascript')
    <script>
        window.onload = () => {
            $(function () {
                $('.save-cron').submit(function (e) {
                    e.preventDefault();
                    $.post('{{ route('cron.save') }}', $(this).serialize(), function () {

                    })
                });
            })
        }
    </script>
@stop
