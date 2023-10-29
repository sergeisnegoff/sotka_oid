<div class="col-12 col-md-4">
    <div class="box__catalog-sorting" >

        <div class="wrapper-sorting-title d-none d-xl-block">Подкатегория:</div>
        <div>
            <select name="subcategory" id="subcategorySelect"
                    style="width: 100%; border-color: #6dac52; border-radius: 20px;padding: 5px 20px;">
                @foreach($currentCategory->childs as $eachsubCategory)
                <option value="{{$eachsubCategory->id}}" {{$eachsubCategory->id == $currentsubCategory->id ? 'selected' : ''}}>{{$eachsubCategory->title}}</option>
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

    })
</script>
