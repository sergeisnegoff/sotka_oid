@extends('voyager::master')

@section('page_title', __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <div class="panel panel panel-bordered panel-warning">
                    <div class="panel-body">
                        <h5>Активировать аккаунт</h5>
                        <div class="form-group">
                            <?php $class = $options->class ?? "toggleswitch"; ?>
                                <input type="checkbox" name="active" id="active_user"  class="{{ $class }}" value="{{$dataTypeContent->active == 'off' ? 'on' : 'off'}}"
                                       @if($dataTypeContent->active == 'on') checked @endif>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-content container-fluid">
        <form class="form-edit-add" role="form"
              action="@if(!is_null($dataTypeContent->getKey())){{ route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) }}@else{{ route('voyager.'.$dataType->slug.'.store') }}@endif"
              method="POST" enctype="multipart/form-data" autocomplete="off">
            <!-- PUT Method if we are editing -->
            @if(isset($dataTypeContent->id))
                {{ method_field("PUT") }}
            @endif
            {{ csrf_field() }}

            <div class="row">
                <div class="col-md-8">
                    <div class="panel panel-bordered">
                    {{-- <div class="panel"> --}}
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="panel-body">
                            <div class="form-group">
                                <label for="name">{{ __('voyager::generic.name') }}</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('voyager::generic.name') }}"
                                       value="{{ old('name', $dataTypeContent->name ?? '') }}">
                            </div>

                            <input type="hidden" name="manager_table" value="contacts_managers">

                            <div class="form-group">
                                <label for="email">{{ __('voyager::generic.email') }}</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('voyager::generic.email') }}"
                                       value="{{ old('email', $dataTypeContent->email ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="phon">Номер телефона</label>
                                <input type="text" class="form-control" id="phon" name="phon" placeholder="{{ __('voyager::generic.phon') }}"
                                       value="{{ old('email', $dataTypeContent->phon ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="city">Город</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="{{ __('voyager::generic.city') }}"
                                       value="{{ old('email', $dataTypeContent->city ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="personal_sale">Персональная скидка</label>
                                <input type="number" class="form-control" id="personal_sale" name="personal_sale" placeholder="%"
                                       value="{{ old('personal_sale', $dataTypeContent->personal_sale ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="password">{{ __('voyager::generic.password') }}</label>
                                @if(isset($dataTypeContent->password))
                                    <br>
                                    <small>{{ __('voyager::profile.password_hint') }}</small>
                                @endif
                                <input type="password" class="form-control" id="password" name="password" value="" autocomplete="new-password">
                            </div>

                            <div class="form-group">
                                <label for="manager_id">Менеджер</label>
                                @php
                                    $managers = \App\Models\ContactsManagersModel::all();
                                    $supervisor = \App\Models\ContactsSupervisorModel::all();
                                    $managers = $managers->merge($supervisor);
                                @endphp
                                <input type="hidden" name="manager_id" value="{{$dataTypeContent->manager_id}}">
                                <input type="hidden" name="manager_table" value="{{$dataTypeContent->manager_table}}">
                                <select class="form-control" id="manager_morph" onchange="changeManager.call(this)">
                                    <option value="0">- Выбрать -</option>
                                    @foreach ($managers as $contact)
                                        <option value="{{$contact->getTable()}}:{{ $contact->id }}" {{ $contact->id == $dataTypeContent->manager_id && $contact->getTable() == $dataTypeContent->manager_table ? 'selected' : '' }}>{{ $contact->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @can('editRoles', $dataTypeContent)
                                <div class="form-group">
                                    <label for="default_role">{{ __('voyager::profile.role_default') }}</label>
                                    @php
                                        $dataTypeRows = $dataType->{(isset($dataTypeContent->id) ? 'editRows' : 'addRows' )};

                                        $row     = $dataTypeRows->where('field', 'user_belongsto_role_relationship')->first();
                                        $options = $row->details;
                                    @endphp
                                    @include('voyager::formfields.relationship')
                                </div>
                            @endcan

                            @php
                            if (isset($dataTypeContent->locale)) {
                                $selected_locale = $dataTypeContent->locale;
                            } else {
                                $selected_locale = config('app.locale', 'en');
                            }

                            @endphp
                            <div class="form-group">
                                <label for="locale">{{ __('voyager::generic.locale') }}</label>
                                <select class="form-control select2" id="locale" name="locale">
                                    @foreach (Voyager::getLocales() as $locale)
                                    <option value="{{ $locale }}"
                                    {{ ($locale == $selected_locale ? 'selected' : '') }}>{{ $locale }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>


                    <button type="submit" class="btn btn-primary pull-right save">
                        {{ __('voyager::generic.save') }}
                    </button>
                </div>

                <div class="col-md-4">
                    <div class="panel panel panel-bordered panel-warning">
                        <div class="panel-body">
                            <div class="form-group">
                                @if(isset($dataTypeContent->avatar))
                                    <img src="{{ filter_var($dataTypeContent->avatar, FILTER_VALIDATE_URL) ? $dataTypeContent->avatar : Voyager::image( $dataTypeContent->avatar ) }}" style="width:200px; height:auto; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:10px;" />
                                @endif
                                <input type="file" data-name="avatar" name="avatar">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <iframe id="form_target" name="form_target" style="display:none"></iframe>
        <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post" enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
            {{ csrf_field() }}
            <input name="image" id="upload_file" type="file" onchange="$('#my_form').submit();this.value='';">
            <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
        </form>

        @if(isset($dataTypeContent->id))
            @php
                $categories = categoryTreeSort();
                $priceRange = [5000, 10000, 20000, 30000, 40000, 50000, 75000, 100000];

                $values = [];
                \App\Models\UserSaleSystem::where('user_id', $dataTypeContent->id)->get()->each(function ($item) use (&$values) {
                    if (!empty($item->sale))
                        $values[$item->category_id] = $item->sale;
                });

                $checkedCategory = \App\Models\UserSaleSystem::checkedCategories($dataTypeContent->id)->toArray();
            @endphp

            <form class="panel panel-primary" method="post" action="{{ route('voyager.users-categories.updateTable', ['id' => $dataTypeContent->id]) }}">
                @csrf
                <div class="panel-body" style="max-width: 99%;overflow: auto;">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <td colspan="{{ count($categories)+1 }}"><h4>Скидки на объём</h4></td>
                        </tr>
                        </thead>
                        <tr id="categories">
                            @foreach ($categories as $category)
                                <td> <label for=""><input type="checkbox" {{ in_array($category['id'], $checkedCategory) ? 'checked' : '' }} name="category[{{ $category['id'] }}]" data-id="{{ $category['id'] }}" id="">&nbsp;{{ $category['title'] }}</label> (%)</td>
                            @endforeach
                        </tr>
                        <tr id="subcategories">
                            @foreach ($categories as $category)
                                <td>
                                    @foreach ($category['children'] as $child)
                                        <label for=""><input type="checkbox" {{ in_array($child['id'], $checkedCategory) || in_array($category['id'], $checkedCategory) ? 'checked' :'' }} name="category[{{ $child['id'] }}]" data-parentid="{{ $category['id'] }}" id="">&nbsp;{{ $child['title'] }}</label> <br />
                                    @endforeach
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($categories as $category)
                                <td><input type="number" style="min-width: 320px;"  name="userRange[{{ $category['id'] }}]" value="{{ @$values[$category['id']] }}" class="form-control"><input
                                        type="hidden" name="category_childs[{{ $category['id'] }}]" value="{{ implode(',', array_column($category['children'], 'id'))  }}"></td>
                            @endforeach
                        </tr>
                    </table>

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                        </div>
                    </div>
                </div>
            </form>

            @php
                $brands = \App\Models\Brands::all();
                $values = [];
                \App\Models\UserBrandSaleSystem::where('user_id', $dataTypeContent->id)->each(function ($item) use (&$values) {
                    $values[$item->brand_id] = $item->sale;
                });

                $checkedBrands = \App\Models\UserBrandSaleSystem::checkedBrands($dataTypeContent->id)->toArray();
            @endphp
            <form class="panel panel-primary" method="post" action="{{ route('voyager.users-brands.updateTable', ['id' => $dataTypeContent->id]) }}">
            @csrf
            <div class="panel-body" style="max-width: 99%;overflow: auto;">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td colspan="{{ count($brands)+1 }}"><h4>Скидки на объём</h4></td>
                    </tr>
                    </thead>
                    <tr>
                        @foreach ($brands as $brand)
                            <td> <label for=""><input type="checkbox" {{ in_array($brand->id, $checkedBrands) ? 'checked' : '' }} name="brands[{{ $brand->id }}]" id="">&nbsp;{{ $brand->title }}</label> (%)</td>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($brands as $brand)
                            <td><input style="min-width: 120px;" type="number" name="priceRange[{{ $brand->id }}]" value="{{ @$values[$brand->id] }}" class="form-control"></td>
                        @endforeach
                    </tr>
                </table>

                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </div>
            </div>
        </form>
        @endif
    </div>
@stop

@section('javascript')
    <script>
        $('document').ready(function () {
            $('#categories input[type=checkbox]').click(function () {
                let index = $(this).closest('td').index() + 1;

                $('#subcategories td:nth-child('+index+') input[type=checkbox]').each(function () {
                    $(this).attr('checked', ($(this).is(':checked') ? false : true))
                })
            });

            $('.toggleswitch').bootstrapToggle();
            $("#active_user").change(function() {
                $.ajax({
                    type:'POST',
                    url:'/users/active/'+{{$dataTypeContent->id}},
                    data: { "active" : $(this).val() },
                    success: function(data){
                        window.location.reload();
                    }
                });
            });
        });

        function changeManager(){
            let self = $(this),
                val = self.val().split(':'),
                ns = self.closest('.form-group'),
                manager_table = ns.find('[name="manager_table"]'),
                manager_id = ns.find('[name="manager_id"]');
            if (val.length) {
                manager_table.val(val[0]);
                manager_id.val(val[1]);
            } else {
                manager_table.val('');
                manager_id.val('');
            }
        }
    </script>
@stop

