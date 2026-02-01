@php
    $f = $filters ?? [];
    $selectedRoles = $f['roles'] ?? [];
    $roles = \App\Orchid\Models\Role::all();
    $managers = \App\Models\ContactsManagersModel::all();
@endphp

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3 align-items-end">

            <div class="col-md-3">
                <label class="form-label">Роль</label>
                <select class="form-control" id="f_roles">
                    <option value="">Все</option>
                    @foreach($roles as $r)
                        @php $label = $r->display_name ?: $r->name; @endphp
                        <option value="{{ $r->id }}" @if(in_array($r->id, $selectedRoles)) selected @endif>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Менеджер</label>
                <select class="form-control" id="f_manager">
                    <option value="">Все</option>
                    @foreach($managers as $m)
                        <option value="{{ $m->id }}" @if(($f['manager_id'] ?? '') == $m->id) selected @endif>
                            {{ $m->name }} @if(!$m->visible) (скрыт)@endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Дата создания с</label>
                <input type="date" class="form-control" id="f_from" value="{{ $f['created_from'] ?? '' }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">по</label>
                <input type="date" class="form-control" id="f_to" value="{{ $f['created_to'] ?? '' }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Поле</label>
                <select class="form-control" id="f_field">
                    <option value="" @if(($f['field'] ?? '')==='') selected @endif>Искать по всем полям</option>
                    <option value="name"  @if(($f['field'] ?? '')==='name') selected @endif>Имя</option>
                    <option value="email" @if(($f['field'] ?? '')==='email') selected @endif>Email</option>
                    <option value="phon"  @if(($f['field'] ?? '')==='phon') selected @endif>Телефон</option>
                    <option value="city"  @if(($f['field'] ?? '')==='city') selected @endif>Город</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Запрос</label>
                <input type="text" class="form-control" id="f_q" placeholder="Поиск..." value="{{ $f['q'] ?? '' }}">
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="button" class="btn btn-primary flex-fill" id="filters_apply">Применить</button>
                <a class="btn btn-light flex-fill" href="{{ url()->current() }}">Сбросить</a>
            </div>

        </div>
    </div>
</div>

<script>
    (function () {
        const base = "{{ url()->current() }}";

        function getSelectedValues(select) {
            return Array.from(select.selectedOptions).map(o => o.value).filter(Boolean);
        }

        document.getElementById('filters_apply')?.addEventListener('click', function () {
            const roles = getSelectedValues(document.getElementById('f_roles'));
            const params = new URLSearchParams();

            roles.forEach(id => params.append('filters[roles][]', id));

            const manager = document.getElementById('f_manager').value;
            if (manager) params.set('filters[manager_id]', manager);

            const from = document.getElementById('f_from').value;
            if (from) params.set('filters[created_from]', from);

            const to = document.getElementById('f_to').value;
            if (to) params.set('filters[created_to]', to);

            const field = document.getElementById('f_field').value;
            if (field) params.set('filters[field]', field);

            const q = document.getElementById('f_q').value.trim();
            if (q) params.set('filters[q]', q);

            const url = base + (params.toString() ? ('?' + params.toString()) : '');

            // убиваем confirm “изменения могут не сохраниться”
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
                document.getElementById('filters_apply')?.click();
            }
        });
    })();
</script>

