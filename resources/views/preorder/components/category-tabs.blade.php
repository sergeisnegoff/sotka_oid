<div class="row">
    <div class="col-12">
        <div class="box__tab-prsonalarea d-none d-sm-flex" style="padding-top: 20px;">
            <div class="box__tab-items">
                <ul class="d-flex" style="flex-flow: row wrap;">
                    @foreach ($categories as $category)
                        <li class="{{ $currentCategory->id == $category->id ? 'active' : '' }}" style="padding-top:18px;"><a href="/preorders/{{$preorder->id.'/category/?category='.$category->id }}">{{$category->title}}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="box__tab-prsonalarea d-md-none" style="padding-top: 5px;">
            <h4>Категории предзаказа</h4>
            <ul class="d-flex" style="flex-flow: wrap; flex-direction: column">
                @foreach ($categories as $category)
                    <li class="{{ $currentCategory->id == $category->id ? 'active' : '' }}" style="padding: 10px 0px 5px 0px; margin: 0"><a href="/preorders/{{$preorder->id.'/category/?category='.$category->id }}">{{$category->title}}</a></li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
