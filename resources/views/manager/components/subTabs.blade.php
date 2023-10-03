<div class="row">
    <div class="col-12">
        <div class="box__tab-prsonalarea" style="padding-top:20px!important;">
            <div class="box__tab-active">
                {{ match ($subPage) {
                    'index' => 'Все клиенты',
                    'orders' => 'Заказы клиентов',
                    'preorders' => 'Предзаказы клиентов',

                    }
                }}
            </div>
            <div class="box__tab-items">
                <ul>
                    <li class="{{ $subPage == 'index' ? 'active' : '' }}"><a  style="font-size:0.8rem!important;" href="{{ route('manager.clients') }}">Все клиенты</a></li>
                    <li class="{{ $subPage == 'orders' ? 'active' : '' }}"><a  style="font-size:0.8rem!important;"
                            href="{{ route('manager.clients.orders') }}">Заказы клиентов</a></li>
                    <li class="{{ $subPage == 'preorders' ? 'active' : '' }}"><a style="font-size:0.8rem!important;"
                            href="{{ route('manager.clients.preorders') }}">Предзаказы клиентов</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
