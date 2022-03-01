<ul>
    @foreach($categories as $category)
        <li>
            <a href="/products/{{$category['title']}}">{{ mb_ucfirst(mb_strtolower($category['title'])) }}</a>
            @if (!empty($category['children']))
                @php($category['children'] = collect($category['children'])->chunk(10))
                <span></span>

                <div class="dropdown-categories">

                    @foreach ($category['children'] as $chunk)
                        <div class="dropdown-categories__column">
                            @foreach ($chunk as $child)
                                <div><a href="/products/{{$category['id']}}/{{ $child['title'] }}"
                                   style="text-transform: capitalize;">{{ mb_strtolower($child['title']) }}</a>
                                    <span>{{ $child['productsCount'] }}</span></div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif
        </li>
    @endforeach
</ul>
