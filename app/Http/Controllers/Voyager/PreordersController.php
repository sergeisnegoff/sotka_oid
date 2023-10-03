<?php

namespace App\Http\Controllers\Voyager;

use App\Jobs\GetDataFromExcelJob;
use App\Jobs\ParsePreorderFileJob;
use App\Models\Preorder;
use App\Models\PreorderSheetMarkup;
use App\Models\PreorderTableSheet;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class PreordersController extends VoyagerBaseController
{

    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $code = [
            'code' => Str::slug($request->title),
        ];

        \Validator::make($code, [
            'code' => 'unique:preorders,code'
        ], [
            'unique' => 'Поля название уже существует',
        ])->validate();

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();

        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        event(new BreadDataAdded($dataType, $data));

        if (!$request->has('_tagging')) {
            if (auth()->user()->can('browse', $data)) {
                $redirect = redirect()->route("voyager.{$dataType->slug}.index");
            } else {
                $redirect = redirect()->back();
            }

            if ($request->has('file')) {
                ParsePreorderFileJob::dispatch($data);
            }

            return $redirect->with([
                'message'    => __(
                        'voyager::generic.successfully_added_new'
                    ) . " {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
    }

    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $preorderTableSheets = [];
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);
            $query = $model->query();

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $query = $query->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists(
                    $model,
                    'scope' . ucfirst($dataType->scope)
                )) {
                $query = $query->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$query, 'findOrFail'], $id);

            $preorderTableSheets = $dataTypeContent->sheets()->with('markup')->get();
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'edit', $isModelTranslatable);

        //ParsePreorderFileJob::dispatch($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return Voyager::view(
            $view,
            compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'preorderTableSheets')
        );
    }

    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $code = [
            'code' => Str::slug($request->title),
        ];

        \Validator::make($code, [
            'code' => Rule::unique('preorders', 'code')->ignore($id)
        ], [
            'unique' => 'Поля название уже существует',
        ])->validate();

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);

        $query = $model->query();

        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
            $query = $query->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $query = $query->withTrashed();
        }

        $data = $query->findOrFail($id);

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();

        // Get fields with images to remove before updating and make a copy of $data
        $to_remove = $dataType->editRows->where('type', 'image')
            ->filter(function ($item, $key) use ($request) {
                return $request->hasFile($item->field);
            });
        $original_data = clone($data);

        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        foreach ($request->get('sheets') as $sheetId => $sheet) {
            if (!isset($sheet['active'])) {
                continue;
            }

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
            ])->validateWithBag('sheets');

            $preorderSheet = PreorderTableSheet::query()
                ->where('id', $sheetId)
                ->first();

            if (!$preorderSheet) {
                continue;
            }

            $preorderSheet->fill([
                'hard_limit' => $sheet['hard_limit'],
                'soft_limit' => $sheet['soft_limit'],
            ]);
            $preorderSheet->save();

            PreorderSheetMarkup::query()
                ->where('preorder_sheet_id', $sheetId)
                ->delete();

            $preorderSheetMarkup = new PreorderSheetMarkup();
            $preorderSheetMarkup->fill(array_merge($sheet, ['preorder_sheet_id' => $sheetId]));

            $preorderSheetMarkup->save();

            $preorderSheet->update([
                'active' => $sheet['active']
            ]);
            $preorder = Preorder::find($id);
            if (!$preorder->file_processed) {
                $preorder->update(['file_processed' => true]);
                Artisan::call('test:getdata ' . $preorderSheet->id);
            }
        }

        // Delete Images
        $this->deleteBreadImages($original_data, $to_remove);

        event(new BreadDataUpdated($dataType, $data));

        if (auth()->user()->can('browse', app($dataType->model_name))) {
            $redirect = redirect()->route("voyager.{$dataType->slug}.index");
        } else {
            $redirect = redirect()->back();
        }

        return $redirect->with([
            'message'    => __(
                    'voyager::generic.successfully_updated'
                ) . " {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }
}
