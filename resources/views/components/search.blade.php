<ul class="list-group search-drop">
    @foreach ($seeds as $key => $s)
        <li class="list-group-item d-flex justify-content-between align-items-center " style="margin:0">
            <a href="/product/{{ $s->id }}">
                <table>
                    <tr>
                        <td>
                            @if (!empty($s->images))
                                <img
                                    src="{{ thumbImg( $s->images, 43, 62, true) }}"
                                    class="img-fluid"
                                    alt="{{ $s->title }}">
                            @endif
                        </td>
                        <td style="text-align: left">
                            {{ $s->title }}<br />
                            @if (!\Illuminate\Support\Facades\Auth::check())
                                <strong></strong>
                            @else
                                <strong>{{ $s->price }} ₽</strong>
                            @endif
                        </td>
                    </tr>
                </table>
            </a>
        </li>
        @if ($key == 4)
            <li class="list-group-item d-flex justify-content-between align-items-center " style="margin:0">
                <button class="btn" type="submit"> Посмотреть остальные</button>
            </li>
            @break
        @endif
    @endforeach
</ul>
