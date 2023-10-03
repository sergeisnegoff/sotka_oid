<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportPreorderXlsx implements FromView
{
    private $preorder;

    public function __construct($preorder)
    {
        $this->preorder = $preorder;
    }

    public function view(): View
    {
        return view('export.preorder-xls', [
            'preorder' => $this->preorder
        ]);
    }
}
