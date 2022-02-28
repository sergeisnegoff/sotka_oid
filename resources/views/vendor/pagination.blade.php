<div class="row">
    <div class="col-12">
        <div class="text-center">
            @if ($paginator->lastPage() > 1)
                <ul class="pagination">
                    <li class="{{ ($paginator->currentPage() == 1) ? ' disabled' : '' }} prev">
                        <a href="{{ $paginator->url(max($paginator->currentPage()-1, 1)) }}">Предыдущая</a>
                    </li>
                    <li class="{{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }} next">
                        <a href="{{ $paginator->url($paginator->currentPage()+1) }}">Следующая</a>
                    </li>
                </ul>
            @endif
        </div>
    </div>
</div>
