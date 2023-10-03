<div class="box__popup-filter" data-popup="filter">
    <div class="wrapper-popup">
        <div class="btn__close">
            <button aria-label="Закрыть попап" data-btn-closepopup><span></span></button>
        </div>
        <div class="wrapper-popupfilter-top">
            <div class="row">
                <div class="col-12">
                    <h2>Фильтр</h2>
                </div>
            </div>
        </div>
        <div class="box__form">
            <div class="wrapper-popupfilter-center">
                @foreach(\App\Models\Filter::all() as $filter)
                    <div class="warpper__filter-item active">
                        <div class="wrapper__filter-title">
                            <div class="row">
                                <div class="col-12">
                                    <h4> {{$filter->title}}</h4>
                                </div>
                            </div>
                        </div>
                        @foreach (\App\Models\Subfilter::all() as $subFilter)
                            @if ($subFilter->filter_id == $filter->id)
                                <div class="wrapper__filter-content">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="box__checkbox">
                                                <div class="wrapper-checkbox">
                                                    <label>

                                                        <input name="filter[]" class="filterShow filterChecked"
                                                               type="checkbox" id="{{$subFilter->title}}" value="{{$subFilter->title}}">
                                                        <span>
                                                    <span class="box__checkbox-icon"></span>
                                                    <span class="box__checkbox-text">{{$subFilter->title}}</span>
                                                </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
            <!--   <div class="wrapper-popupfilter-bottom">
                   <div class="btn btn-white">
                       <button>Сбросить</button>
                   </div>
                   <div class="btn">
                       <button>Применить</button>
                   </div>
               </div> -->
        </div>
    </div>
</div>
