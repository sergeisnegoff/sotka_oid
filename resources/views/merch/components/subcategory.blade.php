<div class="col-12 col-md-4">
    <div class="box__catalog-sorting" style="margin-top:25px;">

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
<script>
    $(document).ready(function () {
        $('#subcategorySelect').change(function () {
            const value = $(this).val()
            let url = new URL(window.location.href);
                url.searchParams.set('subcategory', value);
            window.location.href = url.href;
        })
    })
</script>
