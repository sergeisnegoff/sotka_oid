<div class="col-12">
    <div class="row">
        <div class="col-12 text-center">
            <h2>{{ !isset($item) ? 'Добавление адреса' : 'Редактирование адреса' }}</h2>
        </div>
    </div>
    <div class="box__form">
        <form method="post" action="{{  !isset($item) ? route('profile.address.store') : route('profile.address.update', ['id' => $item->id]) }}">
            @csrf
            @if (isset($item))
                @method('PATCH')
            @else
                @method('PUT')
            @endif
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <div class="row">
                <div class="col-12">
                    <div class="box__input"><input type="text" value="{{ @$item->region }}" class="region-autocomplete" name="region" placeholder="Регион"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="box__input"><input type="text" value="{{ @$item->city }}" class="city-autocomplete" name="city" placeholder="Населённый пункт"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="box__input"><input type="text" value="{{ @$item->address }}" class="address-autocomplete" name="address" placeholder="Улица"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="box__input"><input type="text" value="{{ @$item->house }}" name="house" placeholder="Дом"></div>
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
