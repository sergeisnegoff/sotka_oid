<div class="col-12 col-md-4">
<div class="box__input row" style="margin: 0">
    <div class="col-9">
        <input type="text"  value="{{$filterName ?? ''}}" class="searchTitle" id="find-clients-input"
               placeholder="Найти по имени">
    </div>
    <div class="col-3">
    <div class="btn" style="">
        <a href="#" id="find-clients-btn">Найти</a>
    </div>
    </div>
</div>
</div>
<script>
    $(document).ready(function () {
        $('#find-clients-btn').click(function () {
            search()
        })
        $('#find-clients-input').on('keypress', function (e) {
            if (e.which === 13) {
                search()
            }
        })
    })

    function search() {
        const value = $('#find-clients-input').val()
        let url = new URL(window.location.href);
        if (value === '')
            url.searchParams.delete('name')
        else
            url.searchParams.set('name', value);
        window.location.href = url.href;
    }
</script>
