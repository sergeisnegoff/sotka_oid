<div class="col-12">
    <div class="row">
        <div class="col-12 text-center">
            <h2>{{ !isset($item) ? 'Добавление адреса' : 'Редактирование адреса' }}</h2>
        </div>
    </div>
    <div class="box__form">
        <form method="post" action="{{  !isset($item) ? route('profile.address.store') : route('profile.address.update', ['id' => $item->id]) }}"  autocomplete="off">
            @csrf
            @if (isset($item))
                @method('PATCH')
            @else
                @method('PUT')
            @endif
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <div class="row">
                <div class="col-12">
                    <div class="box__input">
                        <input type="text" value="{{ @$item->region }}" class="region-autocomplete step" autocomplete="off" required name="region" placeholder="Регион">
                        <input type="hidden" name="region_id" value="{{ @$item->region_id }}">
                        <div class="err"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="box__input">
                        <input type="text" value="{{ @$item->city }}" class="city-autocomplete step" autocomplete="off" required name="city" placeholder="Населённый пункт" readonly>
                        <input type="hidden" name="city_id" value="{{ @$item->city_id }}">
                        <div class="err"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="box__input">
                        <input type="text" value="{{ @$item->address }}" class="street-autocomplete step" autocomplete="off" required name="address" placeholder="Улица" readonly>
                        <input type="hidden" name="address_id" value="{{ @$item->address_id }}">
                        <div class="err"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="box__input">
                        <input type="text" value="{{ @$item->house }}" class="building-autocomplete step" autocomplete="off" required name="house" placeholder="Дом" readonly>
                        <div class="err"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="btn" style="margin-top: 25px;">
                        <button>{{ !isset($item) ? 'Добавить адрес' : 'Редактировать адрес' }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<style>
    .err {
        color: red;
        font-size: 12px;
    }
</style>
<script>
    // The change event is fired when a form element loses focus
    // and its value has changed since the last time we interacted with it
    // $(".step").focusout(function(){
    //     var all_next_steps = $(this).parent().parent().parent().nextAll().find('.step');
    //     if ($(this).is('readonly', false)) {
    //         all_next_steps.parent().find('.err').text('');
    //     }
    // });
    $('.step').click(function() {
        var next_step = $(this).parent().parent().parent().next().find('.step');
        var prev_step = $(this).parent().parent().parent().prev().find('.step');
        var all_next_steps = $(this).parent().parent().parent().nextAll().find('.step');
        if($(this).is('[readonly]') && prev_step.val().length === 0) {
            $(this).parent().find('.err').text('Вернитесь на предыдущий шаг');
        } else {
            all_next_steps.parent().find('.err').text('');
            $(this).attr('readonly', false);
        }
        if ($(this).filter("[name=house]").val().length > 5) {
            $(this).parent().find('.err').text('Введите корректный адрес');
        } else {
            if($(this).is('[readonly]') && prev_step.val().length === 0) {
                $(this).parent().find('.err').text('Вернитесь на предыдущий шаг');
            } else {
            $(this).parent().find('.err').text('');
            }
        }
    });
    $('.step').change(function() {
        var next_step = $(this).parent().parent().parent().next().find('.step');
        var all_next_steps = $(this).parent().parent().parent().nextAll().find('.step');
        var autoclick = false;
        // If the element *has* a value
        if ($(this).val()) {
            // Should also perform validation here
            $('.autoComplete_result').click(function() {
                autoclick = true;
                if(autoclick){
                    next_step.attr('readonly', false);
                }
                return true;
            });
            $(this).val('');
        } else {
            all_next_steps.val('');
            all_next_steps.attr('readonly', true);
        }
        if ($(this).filter("[name=house]").val().length > 5) {
            $(this).parent().find('.err').text('Введите корректный адрес');
            $(this).val('');
        } else {
            $(this).parent().find('.err').text('');
        }
    });
</script>
