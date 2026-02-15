<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Preorder;

use App\Jobs\ParsePreorderFileJob;
use App\Models\Preorder;
use App\Models\PreorderSheetMarkup;
use App\Models\PreorderTableSheet;
use App\Orchid\Layouts\Preorder\PreorderEditLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class PreorderEditScreen extends Screen
{
    public $preorder;

    public function query(Preorder $preorder): iterable
    {
        $sheets = collect();
        $isInternal = false;

        if ($preorder->exists) {
            $sheets = $preorder->sheets()->with('markup')->get();
            $isInternal = (bool) $preorder->is_internal;
        }

        return [
            'preorder'        => $preorder,
            'preorderSheets'  => $sheets,
            'isInternal'      => $isInternal,

            // Картинки: относительный путь → полный URL для Picture
            'image_url'            => $this->toStorageUrl($preorder->image),
            'background_image_url' => $this->toStorageUrl($preorder->background_image),
            'default_image_url'    => $this->toStorageUrl($preorder->default_image),

            // Файлы: Voyager JSON → attachment IDs для Upload
            'upload_file'         => $this->voyagerJsonToAttachmentIds($preorder->file),
            'upload_client_file'  => $this->voyagerJsonToAttachmentIds($preorder->client_file),
            'upload_merch_file'   => $this->voyagerJsonToAttachmentIds($preorder->merch_file),

            // Слайд-изображения: Voyager JSON → attachment IDs
            'upload_slide_images' => $this->voyagerJsonToAttachmentIds($preorder->slide_images),
        ];
    }

    public function name(): ?string
    {
        return $this->preorder->exists ? 'Редактирование предзаказа' : 'Создание предзаказа';
    }

    public function description(): ?string
    {
        return $this->preorder->exists
            ? $this->preorder->title
            : 'Заполните данные нового предзаказа';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.products',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Назад')
                ->icon('bs.arrow-left')
                ->route('platform.systems.preorders'),

            Button::make('Удалить')
                ->icon('bs.trash3')
                ->confirm('Удаление предзаказа приведёт к потере всех связанных данных. Продолжить?')
                ->method('remove')
                ->canSee($this->preorder->exists),

            Button::make('Сохранить')
                ->type(Color::PRIMARY())
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    public function layout(): iterable
    {
        $layouts = [
            PreorderEditLayout::class,
        ];

        if ($this->preorder->exists) {
            $layouts[] = Layout::view('orchid.preorders.sheets');
        }

        return $layouts;
    }

    public function save(Preorder $preorder, Request $request)
    {
        $request->validate([
            'preorder.title'             => ['required', 'string', 'max:255'],
            'preorder.description'       => ['nullable', 'string'],
            'preorder.short_description' => ['nullable', 'string'],
            'preorder.end_date'          => ['nullable', 'date'],
            'preorder.min_order'         => ['nullable', 'numeric', 'min:0'],
            'preorder.prepay_percent'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'preorder.is_internal'       => ['nullable'],
            'preorder.is_finished'       => ['nullable'],
            'preorder.is_one_c'          => ['nullable'],
        ]);

        $data = $request->collect('preorder')->toArray();
        $isNew = !$preorder->exists;

        // code генерируется при создании (boot модели), при редактировании обновляем
        if (!$isNew && isset($data['title'])) {
            $data['code'] = Str::slug($data['title']);
        }

        // Булевы поля
        $data['is_internal'] = filter_var($data['is_internal'] ?? false, FILTER_VALIDATE_BOOL);
        $data['is_finished'] = filter_var($data['is_finished'] ?? false, FILTER_VALIDATE_BOOL);
        $data['is_one_c']    = filter_var($data['is_one_c'] ?? false, FILTER_VALIDATE_BOOL);

        // Картинки: полный URL → относительный путь
        $data['image']            = $this->fromStorageUrl($request->input('image_url'));
        $data['background_image'] = $this->fromStorageUrl($request->input('background_image_url'));
        $data['default_image']    = $this->fromStorageUrl($request->input('default_image_url'));

        // Файлы: attachment IDs → Voyager JSON
        $data['file']         = $this->attachmentIdsToVoyagerJson($request->input('upload_file'));
        $data['client_file']  = $this->attachmentIdsToVoyagerJson($request->input('upload_client_file'));
        $data['merch_file']   = $this->attachmentIdsToVoyagerJson($request->input('upload_merch_file'));
        $data['slide_images'] = $this->attachmentIdsToVoyagerJson($request->input('upload_slide_images'));

        $preorder->fill($data)->save();

        // Для нового предзаказа: запуск парсинга файла
        if ($isNew && !empty($preorder->file)) {
            ParsePreorderFileJob::dispatch($preorder);
        }

        // Обработка листов (только при редактировании)
        $sheetsData = $request->input('sheets', []);
        if (!empty($sheetsData) && !$isNew) {
            $this->processSheets($preorder, $sheetsData);
        }

        Toast::info('Предзаказ сохранён.');

        return redirect()->route('platform.systems.preorders.edit', $preorder->id);
    }

    // ── Картинки: конвертация путей ─────────────────────────────────

    protected function toStorageUrl(?string $relativePath): string
    {
        $path = trim((string) $relativePath);

        if ($path === '') {
            return '';
        }

        // Уже полный URL
        if (preg_match('~^https?://~i', $path)) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    protected function fromStorageUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        $storagePrefix = rtrim(asset('storage'), '/') . '/';
        if (str_starts_with($url, $storagePrefix)) {
            return ltrim(substr($url, strlen($storagePrefix)), '/');
        }

        // Уже относительный путь или внешний URL — сохраняем как есть
        return $url;
    }

    // ── Файлы: конвертация Voyager JSON ↔ Orchid Attachments ────────

    protected function voyagerJsonToAttachmentIds(?string $json): array
    {
        if (empty($json)) {
            return [];
        }

        $items = json_decode($json, true);
        if (!is_array($items)) {
            return [];
        }

        $ids = [];

        foreach ($items as $item) {
            $downloadLink = $item['download_link'] ?? '';
            if (empty($downloadLink)) {
                continue;
            }

            // Нормализуем путь (Voyager иногда экранирует слеши)
            $downloadLink = str_replace('\\/', '/', $downloadLink);

            // Orchid physicalPath() = path + name + '.' + extension (без /)
            // поэтому path должен заканчиваться на /
            $path = rtrim(dirname($downloadLink), '/') . '/';
            $filename = pathinfo($downloadLink, PATHINFO_FILENAME);
            $extension = pathinfo($downloadLink, PATHINFO_EXTENSION);

            // Ищем существующий attachment (с trailing / или без)
            $pathWithoutSlash = rtrim($path, '/');
            $attachment = Attachment::where('disk', 'public')
                ->whereIn('path', [$path, $pathWithoutSlash])
                ->where('name', $filename)
                ->where('extension', $extension)
                ->first();

            // Если нашли со старым форматом (без /), обновляем path
            if ($attachment && $attachment->path === $pathWithoutSlash) {
                $attachment->update(['path' => $path]);
            }

            if (!$attachment) {
                $fullPath = storage_path('app/public/' . $downloadLink);
                $size = file_exists($fullPath) ? filesize($fullPath) : 0;

                $attachment = Attachment::create([
                    'name'          => $filename,
                    'original_name' => $item['original_name'] ?? basename($downloadLink),
                    'mime'          => $this->guessMime($extension),
                    'extension'     => $extension,
                    'size'          => $size,
                    'path'          => $path,
                    'disk'          => 'public',
                    'hash'          => md5($downloadLink),
                ]);
            }

            $ids[] = $attachment->id;
        }

        return $ids;
    }

    protected function attachmentIdsToVoyagerJson($ids): ?string
    {
        if (empty($ids)) {
            return null;
        }

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $items = [];

        foreach ($ids as $id) {
            $attachment = Attachment::find($id);
            if (!$attachment) {
                continue;
            }

            // path уже с trailing /, physicalPath() = path + name + '.' + ext
            $items[] = [
                'download_link' => $attachment->path . $attachment->name . '.' . $attachment->extension,
                'original_name' => $attachment->original_name,
            ];
        }

        return !empty($items) ? json_encode($items) : null;
    }

    protected function guessMime(string $extension): string
    {
        return match (strtolower($extension)) {
            'xls'        => 'application/vnd.ms-excel',
            'xlsx'       => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv'        => 'text/csv',
            'jpg', 'jpeg' => 'image/jpeg',
            'png'        => 'image/png',
            'gif'        => 'image/gif',
            'webp'       => 'image/webp',
            default      => 'application/octet-stream',
        };
    }

    // ── Обработка листов ────────────────────────────────────────────

    protected function processSheets(Preorder $preorder, array $sheetsData): void
    {
        foreach ($sheetsData as $sheetId => $sheet) {
            if (!isset($sheet['active'])) {
                continue;
            }

            // Валидация в зависимости от типа предзаказа
            if (!$preorder->is_internal) {
                \Validator::make($sheet, [
                    'category' => 'required',
                    'title'    => 'required',
                    'barcode'  => 'required',
                    'price'    => 'required',
                ], [
                    'required' => 'Поле :attribute обязательно.',
                ], [
                    'category' => 'Категория',
                    'title'    => 'Наименование продукта',
                    'barcode'  => 'Штрихкод',
                    'price'    => 'Цена',
                ])->validate();
            } else {
                \Validator::make($sheet, [
                    'multiplicity' => 'required',
                    'price'        => 'required',
                    'soft_limit'   => 'required',
                    'hard_limit'   => 'required',
                ], [
                    'required' => 'Поле :attribute обязательно.',
                ], [
                    'multiplicity' => 'Кратность',
                    'soft_limit'   => 'Мягкий лимит',
                    'hard_limit'   => 'Жесткий лимит',
                    'price'        => 'Цена',
                ])->validate();
            }

            $preorderSheet = PreorderTableSheet::find($sheetId);
            if (!$preorderSheet) {
                continue;
            }

            // Обновляем лимиты на листе (только для внешних)
            if (!$preorder->is_internal) {
                $preorderSheet->fill([
                    'hard_limit' => $sheet['hard_limit'] ?? null,
                    'soft_limit' => $sheet['soft_limit'] ?? null,
                ]);
            }
            $preorderSheet->save();

            // Обновляем или создаём маппинг
            $markup = PreorderSheetMarkup::query()
                ->where('preorder_table_sheet_id', $sheetId)
                ->first();

            if (!$markup) {
                $markup = new PreorderSheetMarkup();
            }

            $markup->fill(array_merge($sheet, ['preorder_table_sheet_id' => $sheetId]));
            $markup->save();

            // Обновляем статус активности
            $preorderSheet->update(['active' => $sheet['active']]);

            // Запуск обработки данных
            if (!$preorder->is_internal) {
                Artisan::call('test:getdata ' . $preorderSheet->id);
            } else {
                Artisan::call('test:getinternaldata ' . $preorderSheet->id);
            }
        }

        $preorder->update(['file_processed' => true]);
    }

    public function remove(Preorder $preorder)
    {
        $preorder->delete();

        Toast::info('Предзаказ удалён.');

        return redirect()->route('platform.systems.preorders');
    }
}
