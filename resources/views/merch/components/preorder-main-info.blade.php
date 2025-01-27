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
            <td class="preorder-table-data">{{number_format($preorder->quantityByType(), 0, ',', ' ')}}</td>
            <td class="preorder-table-data">{{number_format($preorder->totalByType(false, true), 2, ',', ' ')}}₽</td>
        </tr>
        <tr>
            <td>Сотка</td>
            <td class="preorder-table-data">{{number_format($preorder->quantityByType(true), 0, ',', ' ')}}</td>
            <td class="preorder-table-data">{{number_format($preorder->totalByType(true, true), 2, ',', ' ')}}₽</td>
        </tr>
        <tr>
            <td><b>Всего</b></td>
            <td class="preorder-table-data"><b>{{number_format($preorder->quantity(), 0, ',', ' ')}}</b></td>
            <td class="preorder-table-data"><b>{{number_format($preorder->total(true), 2, ',', ' ')}}₽</b></td>
        </tr>
    </table>
</div>
    <div class="col-12 col-md-4 flex-column">
        <div class="btn">
            <a href="{{route('merch.close-preorder', $preorder->id)}}">Выгрузить и оформить заказ</a>
        </div>
        @if(!$preorder->is_internal && $preorder->is_one_c)
            <div style="display: flex;align-items: center;justify-content: start">
                <div class="btn">
                    <a href="#" id="1c_export" onclick="exportToOneC({{ $preorder->id }})">Выгрузить в 1С</a>

                </div>
                <img id="1c_success" src="{{ url('img/green_success.png') }}" alt="" style="display:none;margin-left: 5px;margin-bottom: 14px;height: 24px">
            </div>
        @endif
        <div class="btn">
            <a href="{{route('preorder.summaryTable', $preorder)}}" target="_blank">Сводная таблица</a>
        </div>
        <form action="{{ route('merch.close-preorder-from-file', $preorder->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            <div  class="form-group-for-file">
                <input type="file" name="table" class="form-control">
                <button class="btn-from-file"
                        type="submit">Оформить из файла</button>
            </div>
        </form>
    </div>
</div>

<script>
    function exportToOneC(preorderId)
    {
        $('#1c_export').css('pointer-events', 'none');
        $.get(`/merch/preorder/${preorderId}/exportOneC`).then((resp) => {
            console.log(resp);
            if(resp.success) {
                $('#1c_success').show();
            }
        })
    }
</script>

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

    .btn-from-file {
        width: 200px;
        position: relative;
        display: inline-block;
        padding: 3px 14px 3px;
        font-family: Jost, sans-serif;
        max-width: 100%;
        text-decoration: none;
        border: none;
        background: var(--color-4);
        border-radius: 30px;
        font-style: normal;
        font-weight: 400;
        font-size: 12px;
        line-height: 18px;
        text-align: center;
        color: var(--color-5);
        cursor: pointer;
        transition: background 0.2s ease-out;
        overflow: hidden;
        z-index: 0;
        margin-top: 10px;
    }
    .btn-from-file:hover {
        background: #598c43;
    }

    .form-group-for-file {
        max-width: 350px;
        padding-top: 30px;
        display: flex;
        flex-direction: column;
    }
</style>
