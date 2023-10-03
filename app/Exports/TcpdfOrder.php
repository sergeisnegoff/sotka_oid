<?php

namespace App\Exports;

use App\Models\Order;

class TcpdfOrder extends TcpdfAbstract
{

    protected $pdf_ns = 'order';
    /**
     * @var Order
     */
    protected $order = null;

    public function pdfConstructor(){
        return [];
    }

    public function pdfShare(){
        $order = $this->order;
        return compact('order');
    }

    public function pdfSettings(){
        $this->SetMargins(15, 11, 15);
        $this->SetHeaderMargin(15);
        $this->SetFooterMargin(15);
        $this->setImageScale(1.25);
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
    }

    public function getSendFilename(){
        return $this->order->hash;
    }

    public function __construct($order, $output = self::TYPE_SHOW){
        $this->order = $order;
        parent::__construct($output);
    }
}
