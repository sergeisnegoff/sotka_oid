@php
    $f = $filters ?? [];
    $parentCategories = \App\Models\Category::where('parent_id', 0)
        ->orWhereNull('parent_id')
        ->orderBy('sorder')
        ->with('category')
        ->get();
    $brands = \App\Models\Brands::orderBy('title')->get();
@endphp

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3 align-items-end">

            <div class="col-md-4">
                <label class="form-label">Поиск</label>
                <input type="text" class="form-control" id="f_q" placeholder="Название, штрихкод, 1С код..." value="{{ $f['q'] ?? '' }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Категория</label>
                <select class="form-control" id="f_category">
                    <option value="">Все</option>
                    @foreach($parentCategories as $parent)
                        <optgroup label="{{ $parent->title }}">
                            <option value="{{ $parent->id }}" @if(($f['category_id'] ?? '') == $parent->id) selected @endif>
                                {{ $parent->title }} (все)
                            </option>
                            @foreach($parent->category as $child)
                                <option value="{{ $child->id }}" @if(($f['category_id'] ?? '') == $child->id) selected @endif>
                                    — {{ $child->title }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Бренд</label>
                <select class="form-control" id="f_brand">
                    <option value="">Все</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" @if(($f['brand_id'] ?? '') == $brand->id) selected @endif>
                            {{ $brand->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="button" class="btn btn-primary flex-fill" id="pf_apply">Применить</button>
                <a class="btn btn-light flex-fill" href="{{ url()->current() }}">Сбросить</a>
            </div>

        </div>
    </div>
</div>

<script>
    (function () {
        const base = "{{ url()->current() }}";

        document.getElementById('pf_apply')?.addEventListener('click', function () {
            const params = new URLSearchParams();

            const q = document.getElementById('f_q').value.trim();
            if (q) params.set('filters[q]', q);

            const cat = document.getElementById('f_category').value;
            if (cat) params.set('filters[category_id]', cat);

            const brand = document.getElementById('f_brand').value;
            if (brand) params.set('filters[brand_id]', brand);

            const url = base + (params.toString() ? ('?' + params.toString()) : '');

            window.onbeforeunload = null;

            if (window.Turbo && typeof window.Turbo.visit === 'function') {
                window.Turbo.visit(url);
            } else {
                window.location.assign(url);
            }
        });

        document.getElementById('f_q')?.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('pf_apply')?.click();
            }
        });
    })();
</script>
