<div class="row">
    <div class="col-12">
        <div class="box__tab-prsonalarea">
            <div class="box__tab-active">{{ $page == 'index' ? 'Личные данные' : ($page == 'current-orders' ? 'Текущие заказы' : ($page == 'order-history' ? 'История заказов':($page == 'basket' ? 'Корзина' : '' ))) }}</div>
            <div class="box__tab-items">
                <ul>
                    <li class="{{ $page == 'index' ? 'active' : '' }}"><a href="{{ route('profile.index') }}">Личные данные</a></li>
                    <li class="{{ $page == 'current-orders' ? 'active' : '' }}"><a href="{{ route('profile.orders.current') }}">Текущие заказы</a></li>
                    <li class="{{ $page == 'order-history' ? 'active' : '' }}"><a href="{{ route('profile.orders.history') }}">История заказов</a></li>
                    <li class="{{ $page == 'basket' ? 'active' : '' }}"><a href="{{ route('profile.orders.cart') }}">Корзина</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
