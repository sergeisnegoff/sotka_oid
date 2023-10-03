<div class="row">
    <div class="col-12">
        <div class="box__tab-prsonalarea">
            <div class="box__tab-active">
                {{ match ($page) {
                    'index' => 'Клиенты',
                    'orders' => 'Заказы',
                    'preorders' => 'Предзаказы',

                    }
                }}
            </div>
            <div class="box__tab-items">
                <ul>
                    <li class="{{ $page == 'index' ? 'active' : '' }}"><a href="{{ route('manager.clients') }}">Клиенты</a></li>
                    <li class="{{ $page == 'orders' ? 'active' : '' }}"><a href="{{ route('manager.orders') }}">Заказы</a></li>
                    <li class="{{ $page == 'preorders' ? 'active' : '' }}"><a href="{{ route('manager.preorders') }}">Предзаказы</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
