@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.($edit ? 'edit' : 'add')).' "'.$dataTypeContent->title.'"' }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-2">
                <div class="list-group" id="pages-list" role="tablist">
                    <a class="list-group-item list-group-item-action active" data-toggle="list" href="#home" role="tab">Основная
                        информация</a>
                    @foreach (\App\Models\PagesBlocksModel::getBlocks($dataTypeContent->id) as $blocks)
                        <a class="list-group-item list-group-item-action other-blocks"
                           data-route="{{ route($blocks->slug) }}" data-toggle="list"
                           href="#{{ \Illuminate\Support\Str::slug($blocks->name) }}" role="tab">{{ $blocks->name }}</a>
                    @endforeach
                </div>
            </div>
            <div class="col-md-10">
                <div class="panel panel-bordered">
                    <div class="tab-content">
                        <div class="tab-pane active" id="home" role="tabpanel">
                            <form role="form"
                                  class="form-edit-add"
                                  action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
                                  method="POST" enctype="multipart/form-data">
                                <!-- PUT Method if we are editing -->
                            @if($edit)
                                {{ method_field("PUT") }}
                            @endif

                            <!-- CSRF TOKEN -->
                                {{ csrf_field() }}

                                <div class="panel-body">

                                    @if (count($errors) > 0)
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                <!-- Adding / Editing -->
                                    @php
                                        $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                                    @endphp

                                    @foreach($dataTypeRows as $row)
                                    <!-- GET THE DISPLAY OPTIONS -->
                                        @php
                                            $display_options = $row->details->display ?? NULL;
                                            if ($dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                                                $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                                            }
                                        @endphp
                                        @if (isset($row->details->legend) && isset($row->details->legend->text))
                                            <legend class="text-{{ $row->details->legend->align ?? 'center' }}"
                                                    style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
                                        @endif

                                        <div
                                            class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                            {{ $row->slugify }}
                                            <label class="control-label"
                                                   for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                            @include('voyager::multilingual.input-hidden-bread-edit-add')
                                            @if (isset($row->details->view))
                                                @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add'), 'view' => ($edit ? 'edit' : 'add'), 'options' => $row->details])
                                            @elseif ($row->type == 'relationship')
                                                @include('voyager::formfields.relationship', ['options' => $row->details])
                                            @else
                                                {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                            @endif

                                            @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                                {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                            @endforeach
                                            @if ($errors->has($row->field))
                                                @foreach ($errors->get($row->field) as $error)
                                                    <span class="help-block">{{ $error }}</span>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endforeach

                                    @if ($dataTypeContent->id == 11)
                                        <div class="form-group col-md-12">
                                            <div id="map" style="width: 100%; height: 400px;"></div>
                                            <input type="hidden" id="coordinates" value="{{ $dataTypeContent->coordinates }}" name="coordinates">
                                        </div>
                                        <script
                                            src="https://api-maps.yandex.ru/2.1/?apikey={{ env('API_KEY_YM') }}&lang=ru_RU"
                                            type="text/javascript"></script>
                                            <script type="text/javascript">
                                                ymaps.ready(init);
                                                @php($coordinates = explode(',', $dataTypeContent->coordinates))
                                                function init() {
                                                    let myMap = new ymaps.Map("map", {
                                                        center: @if (count($coordinates) != 2) [55.76, 37.64] @else [{{ $coordinates[0] }},{{ $coordinates[1] }}] @endif,
                                                        zoom: 7
                                                    });

                                                    @if (count($coordinates) == 2)
                                                        myMap.geoObjects
                                                            .add(new ymaps.GeoObject({
                                                                geometry: {
                                                                    type: "Point",
                                                                    coordinates: [{{ $coordinates[0] }},{{ $coordinates[1] }}]
                                                                },
                                                            }));
                                                    @endif

                                                    let myGeoObject;

                                                    myMap.events.add('click', function (e) {
                                                        var coords = e.get('coords');

                                                        $('#coordinates').val(coords[0]+','+coords[1]);

                                                        myMap.geoObjects.removeAll();
                                                        myGeoObject = new ymaps.GeoObject({
                                                            geometry: {
                                                                type: "Point",
                                                                coordinates: [coords[0], coords[1]]
                                                            },
                                                        });
                                                        myMap.geoObjects
                                                            .add(myGeoObject)
                                                    });
                                                }
                                            </script>
                                    @endif
                                </div><!-- panel-body -->

                                <div class="panel-footer">
                                    @section('submit-buttons')
                                        <button type="submit"
                                                class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                                    @stop
                                    @yield('submit-buttons')
                                </div>
                            </form>
                            <iframe id="form_target" name="form_target" style="display:none"></iframe>
                            <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                                  enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                                <input name="image" id="upload_file" type="file"
                                       onchange="$('#my_form').submit();this.value='';">
                                <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                                {{ csrf_field() }}
                            </form>
                        </div>

                        @foreach (\App\Models\PagesBlocksModel::getBlocks($dataTypeContent->id) as $blocks)
                            <div class="tab-pane ajax-link" id="{{ \Illuminate\Support\Str::slug($blocks->name) }}"
                                 role="tabpanel">

                            </div>
                        @endforeach
                    </div>
                    <!-- form start -->
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;
                    </button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}
                    </h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'
                    </h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger"
                            id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->

    <div id="loadbox" style="display:none;"></div>
@endsection

@section('javascript')
    <script>
        var params = {};
        var $file;
        let deleteUrl = '';

        function deleteHandler(tag, isMulti) {
            return function () {
                $file = $(this).siblings(tag);

                params = {
                    slug: '{{ $dataType->slug }}',
                    filename: $file.data('file-name'),
                    id: $file.data('id'),
                    field: $file.parent().data('field-name'),
                    multi: isMulti,
                    _token: '{{ csrf_token() }}'
                }

                $('.confirm_delete_name').text(params.filename);
                $('#confirm_delete_modal').modal('show');
            };
        }

        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();

            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                } else if (elt.type != 'date') {
                    elt.type = 'text';
                    $(elt).datetimepicker({
                        format: 'L',
                        extraFormats: ['YYYY-MM-DD']
                    }).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if ($isModelTranslatable)
            $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function (i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
            $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
            $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
            $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

            $('#confirm_delete').on('click', function () {
                $.post('{{ route('voyager.'.$dataType->slug.'.media.remove') }}', params, function (response) {
                    if (response
                        && response.data
                        && response.data.status
                        && response.data.status == 200) {

                        toastr.success(response.data.message);
                        $file.parent().fadeOut(300, function () {
                            $(this).remove();
                        })
                    } else {
                        toastr.error("Error removing file.");
                    }
                });

                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();


            let current;

            $('.list-group-item').click(function () {
                current = $(this);
                $(this).addClass('active').siblings().removeClass('active');

                $($(this).attr('href')).addClass('active').siblings().removeClass('active')
            })
            $('body').on('click', '.other-blocks', function (e) {
                let block = $(this).attr('href').replace('#', '');

                current = $(this);
                deleteUrl = current.data('route')+'/__id';

                $(this).addClass('active').siblings().removeClass('active');

                $.get($(this).data('route'), function (html) {
                    let data = $(html).find('.side-body.padding-top').first().removeClass('padding-top side-body'),
                        target = $('.ajax-link#' + block);

                    data.removeClass('padding-top side-body');
                    target.html(data);

                    target.addClass('active').siblings().removeClass('active')
                })
            }).on('click', '.ajax-link a[href].btn:not(.delete)', function (e) {
                e.preventDefault();

                let _self = $(this).closest('.ajax-link');

                $('#loadbox').load($(this).attr('href'), function (html) {
                    let data = $(html).find('.side-body.padding-top').first().removeClass('padding-top side-body');

                    _self.html(data)
                })
            }).on('click', '.ajax-link a[href].btn.delete', function (e) {
                console.log(deleteUrl);
                $('#delete_form')[0].action = deleteUrl.replace('__id', $(this).data('id'));
                $('#delete_modal').modal('show');
            }).on('submit', '.ajax-link form', function (e) {
                e.preventDefault();
                let _self = $(this).closest('.ajax-link'),
                    fd = new FormData($(this)[0]),
                    xhr = new XMLHttpRequest(),
                    modalFade = $('body > .modal-backdrop.fade.in');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    contentType: false,
                    data: fd,
                    xhr: function () {
                        return xhr;
                    },
                    processData: false,
                }).always(() => {
                    let _self = $('.ajax-link.active');

                    $.get(xhr.responseURL, function (html) {
                        let data = $(html).find('.side-body.padding-top').first().removeClass('padding-top side-body');

                        if (modalFade.length) {
                            modalFade.remove();

                            $('body').removeClass('modal-open')
                        }
                        _self.html(data)
                    })
                })
            })
        });
    </script>
@stop
