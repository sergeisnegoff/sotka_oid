<div class="d-flex justify-content-between align-items-end mb-2">
    <form method="GET" action="{{ route('platform.analytics.products') }}" class="d-flex align-items-end gap-2">
        @csrf
        <div>
            <label for="products-period" class="form-label mb-1">Период</label>
            <select id="products-period" name="period" class="form-select">
                @foreach(($periodOptions ?? []) as $value => $label)
                    <option value="{{ $value }}" @selected((string) data_get($filters ?? [], 'period', '90') === (string) $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="products-category" class="form-label mb-1">Категория</label>
            <select id="products-category" name="category_id" class="form-select">
                @foreach(($categoryOptions ?? []) as $value => $label)
                    <option value="{{ $value }}" @selected((string) data_get($filters ?? [], 'category_id', '') === (string) $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="products-sort" class="form-label mb-1">Сортировка</label>
            <select id="products-sort" name="products_sort" class="form-select">
                @foreach(($productSortOptions ?? []) as $value => $label)
                    <option value="{{ $value }}" @selected((string) data_get($filters ?? [], 'products_sort', 'qty') === (string) $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Применить</button>
    </form>
</div>

