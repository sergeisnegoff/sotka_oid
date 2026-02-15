@php
    $sheets = $preorderSheets ?? collect();
    $isInternal = $isInternal ?? false;
@endphp

@if($sheets->isEmpty())
    <div class="mb-3">
        <p class="text-muted">Листы ещё не загружены. Сохраните предзаказ с файлом прайс-листа, чтобы листы были обработаны.</p>
    </div>
@else
    <div class="mb-3">
        <h4 class="mb-3">Настройка листов ({{ $sheets->count() }})</h4>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Название</th>
                        <th class="text-center" style="width: 70px;">Акт.</th>
                        @if(!$isInternal)
                            <th>Штрихкод</th>
                            <th>Категория</th>
                            <th>Подкатегория</th>
                            <th>Наименование</th>
                            <th>Описание</th>
                            <th>Фото</th>
                        @endif
                        <th>Кратность</th>
                        <th>Цена</th>
                        <th>Мягк. лимит</th>
                        <th>Жёстк. лимит</th>
                        @if(!$isInternal)
                            <th>Мин.партия</th>
                            <th>Кратн. ТУ</th>
                            <th>Контейнер</th>
                            <th>Страна</th>
                            <th>Фасовка</th>
                            <th>Тип пакета</th>
                            <th>Вес</th>
                            <th>Сезон</th>
                            <th>Р,И</th>
                            <th>Сезонность</th>
                            <th>Высота</th>
                            <th>Вид упак.</th>
                            <th>Кол-во в упак.</th>
                            <th>Вид культуры</th>
                            <th>Морозост.</th>
                            <th>Доп. 1</th>
                            <th>Доп. 2</th>
                            <th>Доп. 3</th>
                            <th>Доп. 4</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($sheets as $sheet)
                        @php $m = $sheet->markup; @endphp
                        <tr>
                            <td class="text-nowrap">
                                <strong>{{ $sheet->title }}</strong>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" value="1"
                                       name="sheets[{{ $sheet->id }}][active]"
                                       {{ old("sheets.{$sheet->id}.active", $sheet->active) == '1' ? 'checked' : '' }}>
                            </td>

                            @if(!$isInternal)
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][barcode]"
                                           value="{{ old("sheets.{$sheet->id}.barcode", $m->barcode ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][category]"
                                           value="{{ old("sheets.{$sheet->id}.category", $m->category ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][subcategory]"
                                           value="{{ old("sheets.{$sheet->id}.subcategory", $m->subcategory ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][title]"
                                           value="{{ old("sheets.{$sheet->id}.title", $m->title ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][description]"
                                           value="{{ old("sheets.{$sheet->id}.description", $m->description ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][image]"
                                           value="{{ old("sheets.{$sheet->id}.image", $m->image ?? '') }}"></td>
                            @endif

                            <td><input type="text" class="form-control form-control-sm"
                                       name="sheets[{{ $sheet->id }}][multiplicity]"
                                       value="{{ old("sheets.{$sheet->id}.multiplicity", $m->multiplicity ?? '') }}"></td>
                            <td><input type="text" class="form-control form-control-sm"
                                       name="sheets[{{ $sheet->id }}][price]"
                                       value="{{ old("sheets.{$sheet->id}.price", $m->price ?? '') }}"></td>
                            <td><input type="text" class="form-control form-control-sm"
                                       name="sheets[{{ $sheet->id }}][soft_limit]"
                                       value="{{ old("sheets.{$sheet->id}.soft_limit", $m->soft_limit ?? '') }}"></td>
                            <td><input type="text" class="form-control form-control-sm"
                                       name="sheets[{{ $sheet->id }}][hard_limit]"
                                       value="{{ old("sheets.{$sheet->id}.hard_limit", $m->hard_limit ?? '') }}"></td>

                            @if(!$isInternal)
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][moq]"
                                           value="{{ old("sheets.{$sheet->id}.moq", $m->moq ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][multiplicity_tu]"
                                           value="{{ old("sheets.{$sheet->id}.multiplicity_tu", $m->multiplicity_tu ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][container]"
                                           value="{{ old("sheets.{$sheet->id}.container", $m->container ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][country]"
                                           value="{{ old("sheets.{$sheet->id}.country", $m->country ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][packaging]"
                                           value="{{ old("sheets.{$sheet->id}.packaging", $m->packaging ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][package_type]"
                                           value="{{ old("sheets.{$sheet->id}.package_type", $m->package_type ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][weight]"
                                           value="{{ old("sheets.{$sheet->id}.weight", $m->weight ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][season]"
                                           value="{{ old("sheets.{$sheet->id}.season", $m->season ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][r_i]"
                                           value="{{ old("sheets.{$sheet->id}.r_i", $m->r_i ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][seasonality]"
                                           value="{{ old("sheets.{$sheet->id}.seasonality", $m->seasonality ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][plant_height]"
                                           value="{{ old("sheets.{$sheet->id}.plant_height", $m->plant_height ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][packaging_type]"
                                           value="{{ old("sheets.{$sheet->id}.packaging_type", $m->packaging_type ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][package_amount]"
                                           value="{{ old("sheets.{$sheet->id}.package_amount", $m->package_amount ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][culture_type]"
                                           value="{{ old("sheets.{$sheet->id}.culture_type", $m->culture_type ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][frost_resistance]"
                                           value="{{ old("sheets.{$sheet->id}.frost_resistance", $m->frost_resistance ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][additional_1]"
                                           value="{{ old("sheets.{$sheet->id}.additional_1", $m->additional_1 ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][additional_2]"
                                           value="{{ old("sheets.{$sheet->id}.additional_2", $m->additional_2 ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][additional_3]"
                                           value="{{ old("sheets.{$sheet->id}.additional_3", $m->additional_3 ?? '') }}"></td>
                                <td><input type="text" class="form-control form-control-sm"
                                           name="sheets[{{ $sheet->id }}][additional_4]"
                                           value="{{ old("sheets.{$sheet->id}.additional_4", $m->additional_4 ?? '') }}"></td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
