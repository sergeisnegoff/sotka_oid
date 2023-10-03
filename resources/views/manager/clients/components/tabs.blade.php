<div class="row">
    <div class="col-12 row">
        <h4>Клиент: {{$user->name}}</h4>
        <div class="btn create-preorder-btn" data-id="{{$user->id}}" style="margin-left:5px;">

            <a href="#">
                Оформить предзаказ для клиента
            </a>
        </div>
        <script>
            $(document).ready(function() {
                $('.create-preorder-btn').click(function () {
                    const el = $(this)
                    const a = el.children('a')
                    a.html('Обработка...')
                    fetch(`/manager/clients/${el.data('id')}/preorder_as`, {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': `{{ @csrf_token() }}`
                        }
                    })
                        .then(async (resp)=> {
                            if (resp.status === 200) {
                                const text = await resp.text()
                                let checkout = JSON.parse(text)
                                a.html(`Оформлен предзаказ "${checkout.preorder.title}", ID заказа: ${checkout.id}`)
                            }
                        })
                })
            })
        </script>
    </div>
    <div class="col-12">
        <div class="box__tab-prsonalarea" style="padding-top:0;">
            <div class="box__tab-active">
                {{ match ($page) {
                    'order' => 'Заказ #'.$orders[0]->id,
                    'orders' => 'Текущие заказы',
                    'orders_history' => 'История заказов',
                    'upload' => 'Загрузка предзаказа',
                    'preorder_cart' => 'Корзина предзаказа',
                    'preorders_history' => 'История предзаказов'
                    }
                }}
            </div>
            <div class="box__tab-items">
                <ul>
                    @if ($page == 'order')
                        <li class="{{ $page == 'order' ? 'active' : '' }}"><a
                                href="{{ route('manager.clients.showOrder', [$user->id, $orders[0]->id]) }}">Заказ
                                #{{$orders[0]->id}}</a></li>
                    @endif
                    <li class="{{ $page == 'orders' ? 'active' : '' }}"><a
                            href="{{ route('manager.clients.showOrders', $user->id) }}">Текущие заказы пользователя</a>
                    </li>
                    <li class="{{ $page == 'orders_history' ? 'active' : '' }}"><a
                            href="{{ route('manager.clients.showOrdersHistory', $user->id) }}">История заказов</a></li>
                    <li class="{{ $page == 'upload' ? 'active' : '' }}"><a
                            href="{{ route('manager.clients.upload_xlsx', $user->id) }}">Загрузка предзаказа</a></li>
                    <li class="{{ $page == 'preorder_cart' ? 'active' : '' }}"><a
                            href="{{ route('manager.clients.preorder_cart', $user->id) }}">Корзина предзаказа</a></li>
                    <li class="{{ $page == 'preorders_history' ? 'active' : '' }}"><a
                            href="{{ route('manager.clients.showPreordersHistory', $user->id) }}">История
                            предзаказов</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
