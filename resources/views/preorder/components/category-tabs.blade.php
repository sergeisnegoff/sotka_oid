<div class="row">
    <div class="col-12">
        <div class="box__tab-prsonalarea d-none d-sm-flex" style="padding-top: 20px;">
            <div class="box__tab-items">
                <ul class="d-flex" style="flex-flow: row wrap;">
                    @foreach ($categories as $category)
                        <li class="{{ $currentCategory->id == $category->id ? 'active' : '' }}" style="padding-top:18px;">
                            <div class="d-flex" style="flex-direction: column">
                                <a href="/preorders/{{$preorder->id.'/category/?category='.$category->id }}">{{$category->title}}</a>
                                @if($currentCategory->id == $category->id)
                                    <a href="/preorders/category/{{$currentCategory->id}}/products" style="display:inline; border: none" class="btn">
                                        <button>
                                            Все товары категории
                                        </button>
                                    </a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="box__tab-prsonalarea d-md-none" style="padding-top: 5px; text-align: center">
            <hr>
            <h4>Категории предзаказа</h4>
            <ul class="d-flex" style="flex-flow: wrap; flex-direction: column; align-items: center">
                @foreach ($categories as $category)
                    <li class="{{ $currentCategory->id == $category->id ? 'active' : '' }}" style="padding: 10px 0px 5px 0px; margin: 0">
                        <div class="d-flex" style="flex-direction: column; align-items: center">
                            <a href="/preorders/{{$preorder->id.'/category/?category='.$category->id }}">{{$category->title}}</a>
                            @if($currentCategory->id == $category->id)
                                <a href="/preorders/category/{{$currentCategory->id}}/products" style="display:inline; border: none" class="btn">
                                    <button>
                                        Все товары категории
                                    </button>
                                </a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
            <hr>
        </div>
    </div>
</div>
