<div class="row">
    <div class="col-12">
        <div class="box__tab-prsonalarea" style="padding-top: 20px;">
            <div class="box__tab-items">
                <ul class="d-flex" style="flex-flow: row wrap;">
                    @foreach ($categories as $category)
                        <li class="{{ $currentCategory->id == $category->id ? 'active' : '' }}" style="padding-top:18px;"><a href="/preorders/{{$preorder->id.'?category='.$category->id }}">{{$category->title}}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
