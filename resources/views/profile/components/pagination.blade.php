<div class="row">
    <div class="col-12">
        <div class="box__pagination text-center">
            @if ($paginator->lastPage() > 1)
                <ul>
                    <li class="{{ ($paginator->currentPage() == 1) ? ' disabled' : '' }} prev">
                        <a href="{{ $paginator->url(1) }}"><span></span></a>
                    </li>
                    @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                        <li class="{{ ($paginator->currentPage() == $i) ? ' active' : '' }}">
                            <a href="{{ $paginator->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
                    <li class="{{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }} next">
                        <a href="{{ $paginator->url($paginator->currentPage()+1) }}"><span></span></a>
                    </li>
                </ul>
            @endif
        </div>
    </div>
</div>
