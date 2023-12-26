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
        {{ __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
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

                            @if ($errors->sheets->any())
                                <div class="alert alert-danger">
                                    @foreach ($errors->sheets->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </div>
                            @endif

                        <!-- Adding / Editing -->
                            @php
                                $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                            @endphp

                            @foreach($dataTypeRows as $row)
                                @if ($row->field == 'parent_id')
                                    @continue
                                @endif
                            <!-- GET THE DISPLAY OPTIONS -->
                                @php
                                    $display_options = $row->details->display ?? null;
                                    if ($dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                                        $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                                    }
                                @endphp
                                @if (isset($row->details->legend) && isset($row->details->legend->text))
                                    <legend class="text-{{ $row->details->legend->align ?? 'center' }}"
                                            style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
                                @endif

                                <div
                                    class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id))
                                    {{ "id=$display_options->id" }}
                                    @endif>
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

                            @if ($edit)
                                <style>
                                    .table-vertical-heading th {
                                        white-space: nowrap;
                                    }

                                    .table-vertical-heading th {
                                        position: relative;
                                    }

                                    .table-vertical-heading th span {
                                        writing-mode: vertical-lr;
                                        text-orientation: mixed;
                                        transform: rotate(180deg);
                                        white-space: nowrap;
                                    }
                                </style>
                                <table class="table table-hover">
                                    <thead class="table-vertical-heading">
                                    <tr>
                                        <th>Название</th>
                                        <th><span>Активен</span></th>

                                        @if(!$dataTypeContent->is_internal)
                                            <th><span>Штрихкод*</span></th>
                                            <th><span>Категория*</span></th>
                                            <th><span>Подкатегория*</span></th>
                                            <th><span>Наименование*</span></th>
                                            <th><span>Кратность*</span></th>
                                        @endif

                                        @if(!$dataTypeContent->is_internal)
                                            <th><span>Цена*</span></th>
                                            <th><span>Описание</span></th>
                                            <th><span>Фотография</span></th>
                                            <th><span>Мягкий лимит</span></th>
                                            <th><span>Жесткий лимит</span></th>
                                            <th><span>Кратность ТУ</span></th>
                                            <th><span>Контейнер</span></th>
                                            <th><span>Страна</span></th>
                                            <th><span>Фасовка</span></th>
                                            <th><span>Тип пакета</span></th>
                                            <th><span>Вес</span></th>
                                            <th><span>Сезон</span></th>
                                            <th><span>Р,И</span></th>
                                            <th><span>Сезонность</span></th>
                                            <th><span>Высота растения</span></th>
                                            <th><span>Вид упаковки</span></th>
                                            <th><span>Кол-во в упаковке</span></th>
                                            <th><span>Вид культуры</span></th>

                                            <th><span>Морозостойкость</span></th>
                                            <th><span>Доп. 1</span></th>
                                            <th><span>Доп. 2</span></th>
                                            <th><span>Доп. 3</span></th>
                                            <th><span>Доп. 4</span></th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($preorderTableSheets as $sheet)
                                        <tr>
                                            <td>{{ $sheet->title }}</td>
                                            <td>
                                                <input type="checkbox" value="1"
                                                       {{ old("sheets.{$sheet->id}.active", $sheet->active) == '1' ? 'checked' : '' }}
                                                       name="sheets[{{ $sheet->id }}][active]">
                                            </td>
                                            @if(!$dataTypeContent->is_internal)
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][barcode]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->barcode : '' }}"
                                                           class="form-control"></td>
                                                <td>
                                                    <input type="text" name="sheets[{{ $sheet->id }}][category]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->category : '' }}"
                                                           class="form-control">
                                                </td>
                                                <td>
                                                    <input type="text" name="sheets[{{ $sheet->id }}][subcategory]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->subcategory : '' }}"
                                                           class="form-control">
                                                </td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][title]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->title : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][multiplicity]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->multiplicity : '' }}"
                                                           class="form-control"></td>
                                            @endif

                                            @if(!$dataTypeContent->is_internal)
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][price]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->price : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][description]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->description : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][image]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->image : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][soft_limit]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->soft_limit : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][hard_limit]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->hard_limit : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][multiplicity_tu]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->multiplicity_tu : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][container]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->container : '' }}"
                                                           class="form-control"></td>

                                                <td><input type="text" name="sheets[{{ $sheet->id }}][country]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->country : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][packaging]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->packaging : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][package_type]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->package_type : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][weight]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->weight : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][season]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->season : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][r_i]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->r_i : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][seasonality]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->seasonality : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][plant_height]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->plant_height : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][packaging_type]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->packaging_type : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][package_amount]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->package_amount : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][culture_type]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->culture_type : '' }}"
                                                           class="form-control"></td>


                                                <td><input type="text" name="sheets[{{ $sheet->id }}][frost_resistance]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->frost_resistance : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][additional_1]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->additional_1 : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][additional_2]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->additional_2 : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][additional_3]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->additional_3 : '' }}"
                                                           class="form-control"></td>
                                                <td><input type="text" name="sheets[{{ $sheet->id }}][additional_4]"
                                                           value="{{ $sheet->markup !== null ? $sheet->markup->additional_4 : '' }}"
                                                           class="form-control"></td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

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
@stop

@section('javascript')
    <link href="{{ asset('js/admin/tree-table/css/jquery.treetable.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('js/admin/tree-table/jquery.treetable.js') }}"></script>

    <script>
        var params = {};
        var $file;

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
        });
    </script>
@stop
