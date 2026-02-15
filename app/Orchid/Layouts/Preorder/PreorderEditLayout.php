<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Preorder;

use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Layouts\Rows;

class PreorderEditLayout extends Rows
{
    public function fields(): array
    {
        return [
            Input::make('preorder.title')
                ->title('Название')
                ->required()
                ->max(255)
                ->placeholder('Название предзаказа'),

            Quill::make('preorder.description')
                ->title('Описание'),

            Quill::make('preorder.short_description')
                ->title('Краткое описание'),

            Group::make([
                DateTimer::make('preorder.end_date')
                    ->title('Дата окончания')
                    ->format('Y-m-d')
                    ->allowInput(),

                Input::make('preorder.min_order')
                    ->type('number')
                    ->title('Минимальный заказ'),

                Input::make('preorder.prepay_percent')
                    ->type('number')
                    ->title('Процент предоплаты (%)'),
            ]),

            Picture::make('image_url')
                ->title('Изображение-заглушка')
                ->storage('public')
                ->path('preorders/' . date('FY'))
                ->targetUrl(),

            Picture::make('background_image_url')
                ->title('Изображение')
                ->storage('public')
                ->path('preorders/' . date('FY'))
                ->targetUrl(),

            Picture::make('default_image_url')
                ->title('Задний фон')
                ->storage('public')
                ->path('preorders/' . date('FY'))
                ->targetUrl(),

            Upload::make('upload_slide_images')
                ->title('Слайд-изображения')
                ->storage('public')
                ->path('preorders/' . date('FY'))
                ->acceptedFiles('image/*'),

            Group::make([
                CheckBox::make('preorder.is_internal')
                    ->title('Внутренний предзаказ')
                    ->sendTrueOrFalse()
                    ->placeholder('Внутренний'),

                CheckBox::make('preorder.is_finished')
                    ->title('Статус')
                    ->sendTrueOrFalse()
                    ->placeholder('Завершён'),

                CheckBox::make('preorder.is_one_c')
                    ->title('1С')
                    ->sendTrueOrFalse()
                    ->placeholder('Выгружать в 1С'),
            ]),

            Upload::make('upload_file')
                ->title('Файл прайс-листа')
                ->storage('public')
                ->path('preorders/' . date('FY'))
                ->acceptedFiles('.xls,.xlsx,.csv')
                ->maxFiles(1),

            Group::make([
                Upload::make('upload_client_file')
                    ->title('Файл для клиентов')
                    ->storage('public')
                    ->path('preorders/' . date('FY'))
                    ->acceptedFiles('.xls,.xlsx,.csv')
                    ->maxFiles(1),

                Input::make('preorder.client_qty_field')
                    ->title('Клиент: поле кол-ва'),
            ]),

            Group::make([
                Upload::make('upload_merch_file')
                    ->title('Файл для товароведа')
                    ->storage('public')
                    ->path('preorders/' . date('FY'))
                    ->acceptedFiles('.xls,.xlsx,.csv')
                    ->maxFiles(1),

                Input::make('preorder.merch_qty_field')
                    ->title('Товаровед: поле кол-ва'),

                Input::make('preorder.merch_barcode_field')
                    ->title('Товаровед: поле штрихкод'),
            ]),
        ];
    }
}
