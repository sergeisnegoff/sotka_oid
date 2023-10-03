<?php

namespace App\Exports;

class TcpdfPreorder extends TcpdfAbstract
{
    protected $pdf_ns = 'preorder';

    protected $preorder;

    public function pdfConstructor()
    {
        return [];
    }

    public function pdfShare() {
        return ['preorder' => $this->preorder];
    }

    public function pdfSettings()
    {
        $this->SetMargins(15, 11, 15);
        $this->SetHeaderMargin(15);
        $this->SetFooterMargin(15);
        $this->setImageScale(1.25);
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
    }

    public function getSendFilename()
    {
        return $this->preorder->id;
    }

    public function __construct($preorder, $output = self::TYPE_SHOW)
    {
        $this->preorder = $preorder;
        parent::__construct($output);
    }
}
