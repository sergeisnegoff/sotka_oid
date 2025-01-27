<div class="col-12 col-md-4">
    <div class="box__catalog-sorting">

        <div class="wrapper-sorting-title d-none d-xl-block">Подкатегория:</div>
        <div>
            <select name="subcategory" id="subcategorySelect"
                    style="width: 100%; border-color: #6dac52; border-radius: 20px;padding: 5px 20px;">
                @foreach($subCategories as $eachsubCategory)
                    @if(!is_null($eachsubCategory) && !is_null($currentsubCategory))
                    <option
                        value="{{$eachsubCategory->id}}" {{$eachsubCategory->id == $currentsubCategory->id ? 'selected' : ''}}>{{$eachsubCategory->title}}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="col-12 col-md-4">
    <div class="wrapper-sorting-title d-none d-xl-block">Фильтр заказов:</div>
    <div>
        <input name="only-ordered" id="only-ordered-checkbox" type="checkbox" @if($onlyOrdered) checked @endif>
        <label for="only-ordered">Только с заказами</label>
    </div>
</div>
<div class="col-12 col-md-4">
    <div class="wrapper-sorting-title d-none d-xl-block">Поиск:</div>
    <div style="display: flex;align-items: center; justify-content: space-between">
        <input id="search" type="text"
               style="width: 60%;border: 1px solid  #6dac52; border-radius: 20px;padding: 5px 20px;"
               value="{{$search}}">
        <div class="btn" >
            <a id="search_btn" href="#">Поиск</a>
        </div>
        <div class="btn" >
            <a id="reset_btn" href="#">Сброс</a>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#subcategorySelect').change(function () {
            const value = $(this).val()
            let url = new URL(window.location.href);
            url.searchParams.set('subcategory', value);
            window.location.href = url.href;
        })
        $('#only-ordered-checkbox').change(function () {
            const el = $(this)
            let url = new URL(window.location.href)
            if (el.prop('checked')) {
                url.searchParams.set('with_checkouts', 1)
            } else {
                url.searchParams.delete('with_checkouts')
            }
            window.location.href = url.href
        })
        $('#search_btn').on('click', function () {
            let url = new URL(window.location.href);
            const q = $('#search').val();
            console.log(q);
            url.searchParams.set('q', q);
            window.location.href = url.href;
        });
        $('#reset_btn').on('click', function () {
            let url = new URL(window.location.href);
            url.searchParams.delete('q')
            window.location.href = url.href;
        });

    })
</script>
