<div class="col-12 col-md-4">
    <div class="box__catalog-sorting" style="margin:0;">
        <div class="wrapper-sorting-title d-none d-xl-block">Сортировать</div>
        <div>
            <select name="sort" id="sortSelect"
                    style="width: 100%; border-color: #6dac52; border-radius: 20px;padding: 5px 20px;">
                <option value="NONE" {{!$filterSorting ? 'selected' : ''}}>По умолчанию</option>
                <option value="ASC" {{$filterSorting == 'ASC' ? 'selected' : ''}}>Имя (А - Я)</option>
                <option value="DESC" {{$filterSorting == 'DESC' ? 'selected' : ''}}>Имя (Я - А)</option>
            </select>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#sortSelect').change(function () {
            const value = $('#sortSelect').val()
            let url = new URL(window.location.href);
            if (value === 'NONE')
                url.searchParams.delete('sorting')
            else
                url.searchParams.set('sorting', value);
            window.location.href = url.href;
        })
    })
</script>
