@php
    $tree = $tree ?? data_get($discounts ?? [], 'categoriesTree', []);
@endphp

<div class="mb-3">
    <h4 class="mb-2">Скидки по категориям</h4>

    @if(empty($tree))
        <p class="text-muted">Категории не найдены.</p>
    @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                <tr>
                    <th style="width: 40%;">Категория</th>
                    <th style="width: 10%;">Вкл</th>
                    <th style="width: 20%;">Скидка (%)</th>
                    <th style="width: 30%;">Подкатегории</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tree as $pi => $parent)
                    <tr>
                        <td>
                            <strong>{{ $parent['title'] ?? ('Категория #' . ($parent['id'] ?? '')) }}</strong>
                            <input type="hidden" name="discounts[categoriesTree][{{ $pi }}][id]" value="{{ $parent['id'] }}">
                        </td>
                        <td>
                            <input
                                type="checkbox"
                                class="js-parent-toggle"
                                data-parent="{{ $parent['id'] }}"
                                name="discounts[categoriesTree][{{ $pi }}][enabled]"
                                value="1"
                                @if(!empty($parent['enabled'])) checked @endif
                            >
                        </td>
                        <td>
                            <input
                                type="number"
                                class="form-control js-parent-sale"
                                data-parent="{{ $parent['id'] }}"
                                min="0"
                                max="100"
                                step="1"
                                name="discounts[categoriesTree][{{ $pi }}][sale]"
                                value="{{ $parent['sale'] ?? '' }}"
                            >
                        </td>
                        <td>
                            @if(!empty($parent['children']))
                                @foreach($parent['children'] as $ci => $child)
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="flex-grow-1">
                                            {{ $child['title'] ?? ('Категория #' . ($child['id'] ?? '')) }}
                                        </div>

                                        <input type="hidden" name="discounts[categoriesTree][{{ $pi }}][children][{{ $ci }}][id]" value="{{ $child['id'] }}">
                                        <input type="hidden" name="discounts[categoriesTree][{{ $pi }}][children][{{ $ci }}][parent]" value="{{ $parent['id'] }}">

                                        <div class="me-2">
                                            <input
                                                type="checkbox"
                                                class="js-child-toggle"
                                                data-parent="{{ $parent['id'] }}"
                                                name="discounts[categoriesTree][{{ $pi }}][children][{{ $ci }}][enabled]"
                                                value="1"
                                                @if(!empty($child['enabled'])) checked @endif
                                            >
                                        </div>

                                        <div style="width: 110px;">
                                            <input
                                                type="number"
                                                class="form-control js-child-sale"
                                                data-parent="{{ $parent['id'] }}"
                                                min="0"
                                                max="100"
                                                step="1"
                                                name="discounts[categoriesTree][{{ $pi }}][children][{{ $ci }}][sale]"
                                                value="{{ $child['sale'] ?? '' }}"
                                            >
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
    (function () {
        function setChildren(parentId, checked) {
            var nodes = document.querySelectorAll('.js-child-toggle[data-parent="' + parentId + '"]');
            nodes.forEach(function (cb) {
                cb.checked = checked;
            });
        }

        function setChildrenSale(parentId, value) {
            var nodes = document.querySelectorAll('.js-child-sale[data-parent="' + parentId + '"]');
            nodes.forEach(function (inp) {
                inp.value = value;
            });
        }


        document.addEventListener('change', function (e) {
            var t = e.target;
            if (!t) return;

            if (t.classList.contains('js-parent-toggle')) {
                var parentId = t.getAttribute('data-parent');
                setChildren(parentId, t.checked);
            }
        });

        document.addEventListener('input', function (e) {
            var t = e.target;
            if (!t) return;

            if (t.classList.contains('js-parent-sale')) {
                var parentId = t.getAttribute('data-parent');
                setChildrenSale(parentId, t.value);
            }
        });

    })();
</script>
