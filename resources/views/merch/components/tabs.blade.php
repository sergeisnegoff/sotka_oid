<div class="row">
    <div class="col-12">
        <div class="box__tab-prsonalarea">
            <div class="box__tab-active">
                {{ match ($page) {
                    'index' => 'Текущие предзаказы',
                    'history' => 'История предзаказов',
                    }
                }}
            </div>
            <div class="box__tab-items">
                <ul>
                    <li class="{{ $page == 'index' ? 'active' : '' }}"><a href="{{ route('merch.home') }}">Текущие предзаказы</a></li>
                    <li class="{{ $page == 'history' ? 'active' : '' }}"><a href="{{ route('merch.history') }}">История предзаказов</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
