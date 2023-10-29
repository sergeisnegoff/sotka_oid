<div class="row">
    <div class="col-12">
        <div class="box__tab-prsonalarea" style="padding-top: 20px;">
            <div class="box__tab-active">
                {{$currentCategory->title}}
            </div>
            <div class="box__tab-items">
                <ul class="d-flex" style="flex-flow: row wrap;">
                    @foreach ($categories as $category)
                    <li class="{{ $currentsubCategory->preorder_category_id == $category->id ? 'active' : '' }}" style="padding-top:18px;"><a href="{{ route('merch.show-preorder', $preorder->id).'?category='.$category->id }}">{{$category->title}}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
