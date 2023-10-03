<div class="row preorder-main-data" data-preorder-id="{{$preorder->id}}">
<div class="col-12 col-md-8">
    <table class="preorder-table">
        <thead>
        <tr>
            <td>
            </td>
            <td>
                <b>Упаковок</b>
            </td>
            <td>
                <b>Сумма</b>
            </td>
        </tr>
        </thead>
        <tr>
            <td>Клиенты</td>
            <td class="preorder-table-data">{{$preorder->quantityByType()}}</td>
            <td class="preorder-table-data">{{$preorder->totalByType(false, true)}}₽</td>
        </tr>
        <tr>
            <td>Сотка</td>
            <td class="preorder-table-data">{{$preorder->quantityByType(true)}}</td>
            <td class="preorder-table-data">{{$preorder->totalByType(true, true)}}₽</td>
        </tr>
        <tr>
            <td><b>Всего</b></td>
            <td class="preorder-table-data"><b>{{$preorder->quantity()}}</b></td>
            <td class="preorder-table-data"><b>{{$preorder->total(true)}}₽</b></td>
        </tr>
    </table>
</div>
    <div class="col-12 col-md-4 flex-column">
        <div class="btn">
            <a href="{{route('merch.close-preorder', $preorder->id)}}">Выгрузить и оформить заказ</a>
        </div>
        <div class="btn">
            <a href="{{route('preorder.summaryTable', $preorder)}}" target="_blank">Сводная таблица</a>
        </div>

    </div>
</div>

<style>
    table.preorder-table, .preorder-table th, .preorder-table td {
        border: 1px solid black;
    }
    .preorder-table thead tr {
        text-align:center;
    }
    .preorder-table-data {
        text-align:right;
    }
</style>
