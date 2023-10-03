<div class="row" style="margin-top:25px;">
    <div class="col-12">
        <div class="row">
            <div class="btn">
                <a style="margin: 0 10px;"
                   href="{{route('manager.orders')}}?from_date={{\Carbon\Carbon::today()->toDateString()}}&to_date={{\Carbon\Carbon::today()->toDateString()}}">Сегодня</a>
            </div>
            <div class="btn">
                <a style="margin-right:10px;"
                   href="{{route('manager.orders')}}?from_date={{\Carbon\Carbon::yesterday()->toDateString()}}&to_date={{\Carbon\Carbon::yesterday()->toDateString()}}">Вчера</a>
            </div>
            <div class="row">
            <form method="GET" class="row" style="margin-left:15px; margin-right:15px;" id="form-date-filter" action="{{route('manager.orders')}}">
                От   <input type="date" name="from_date" value="{{request()->from_date ?? ''}}"/>    до   <input type="date" name="to_date" value="{{request()->to_date ?? ''}}"/>
                   
                <div class="btn" id="form-date-filter-button">
                    <a href="#">
                        Применить
                    </a>
                </div>
            </form>
            <a style="margin-left:10px;" href="{{route('manager.orders')}}">Сбросить</a>
            </>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#form-date-filter-button').click(function() {
            $('#form-date-filter').submit()
        })
    })
</script>
