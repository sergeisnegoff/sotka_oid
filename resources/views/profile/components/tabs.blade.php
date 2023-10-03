<div class="row">
    <div class="col-12">
        <div class="box__tab-prsonalarea">
            <div class="box__tab-active">
                {{ match ($page) {
                    'index' => 'Личные данные',
                    'current-orders' => 'Текущие заказы',
                    'order-history' => 'История заказов',
                    'basket' => 'Корзина',
                    'preorder' => 'Предзаказ',
                    'preorder_history' => 'История предзаказов',
                    'preorder_upload' => 'Загрузка предзаказа'
                    }
                }}
            </div>
            <div class="box__tab-items">
                <ul>
                    <li class="{{ $page == 'index' ? 'active' : '' }}"><a href="{{ route('profile.index') }}">Личные данные</a></li>
                    <li class="{{ $page == 'current-orders' ? 'active' : '' }}"><a href="{{ route('profile.orders.current') }}">Текущие заказы</a></li>
                    <li class="{{ $page == 'order-history' ? 'active' : '' }}"><a href="{{ route('profile.orders.history') }}">История заказов</a></li>
                    <li class="{{ $page == 'basket' ? 'active' : '' }}"><a href="{{ route('profile.orders.cart') }}">Корзина</a></li>
                    <li class="{{ $page == 'preorder' ? 'active' : '' }}"><a href="/preorders/cart">Предзаказ</a></li>
                    <li class="{{ $page == 'preorder_history' ? 'active' : '' }}"><a href="/preorders/history">История предзаказов</a></li>
                    <li class="{{ $page == 'preorder_upload' ? 'active' : '' }}"><a href="/preorders/upload">Загрузка предзаказов</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
