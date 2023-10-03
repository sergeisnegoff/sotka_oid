<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportXls implements FromView
{
    private $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function view(): View
    {
        return view('export.order-xls', [
            'order' => $this->order
        ]);
    }
}
